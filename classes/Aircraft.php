<?php
class Aircraft implements JsonSerializable {
	private $flightno;
	private $model;
	private $location;
	private $altitude;
	private $target_altitude;
	private $heading;
	private $speed;
	private $fields;

	public function __get($property) {
		switch($property) {
			default:
				return $this->$property;
		}
	}

	public function jsonSerialize() {
		return array(
			"flightno" => $this->flightno,
			"model" => $this->model,
			"location" => $this->location,
			"altitude" => $this->altitude,
			"target_altitude" => $this->target_altitude,
			"heading" => $this->heading,
			"speed" => $this->speed,
		);
	}

	public function __construct($flightno, $model, $location, $altitude, $target_altitude, $heading, $speed) {
		$this->fields = array('flightno','model','location','altitude','target_altitude','heading','speed');
		foreach($this->fields as $f) {
			$this->$f = $$f;
		}
	}

	public static function from_redis($flightno) {
		global $redis;
		$data = $redis->hgetall('aircraft:'.$flightno);
		if($data == null) {
			return null;
		}
		$location = array($data['location_x'], $data['location_y']);
		return new Aircraft($flightno, $data['model'], $location, $data['altitude'], $data['target_altitude'], $data['heading'], $data['speed']);
	}

	public function to_redis() {
		global $redis;
		$redis->multi()
			->hset('aircraft:'.$this->flightno, 'model', $this->model)
			->hset('aircraft:'.$this->flightno, 'location_x', $this->location[0])
			->hset('aircraft:'.$this->flightno, 'location_y', $this->location[1])
			->hset('aircraft:'.$this->flightno, 'altitude', $this->altitude)
			->hset('aircraft:'.$this->flightno, 'target_altitude', $this->target_altitude)
			->hset('aircraft:'.$this->flightno, 'heading', $this->heading)
			->hset('aircraft:'.$this->flightno, 'speed', $this->speed)
			->exec();
	}

	public function tick($dt) {
		/* 1 tick = $dt = 1 sekund = 1/60 minut
		 * 1 knop = 1 nm per timme = 1/60 nm per minut
		 */
		$nm = 1/60/60;
		$nm_per_tick = $this->speed * $nm / 60 * $dt;
		$this->location[0] += $nm_per_tick * sin(deg2rad($this->heading));
		$this->location[1] += $nm_per_tick * cos(deg2rad($this->heading));

		$max_fps = 2500/60; // feet, not frames
		if($this->target_altitude != $this->altitude) {
			$diff = $this->target_altitude - $this->altitude;
			if($diff > 0) {
				$this->altitude += min($max_fps, $diff);
			} else {
				$this->altitude += max(-$max_fps, $diff);
			}
		}
	}

	private function set_target_altitude($altitude) {
		if(!is_numeric($altitude) || $altitude > 50 || $altitude < 1) {
			throw new Exception("Bad altitude");
		}
		$this->target_altitude = $altitude * 1000;
	}

	public function chat_response($cmd) {
		$response = array(
			'class' => 'error',
			'msg' => 'message not set',
		);
		try {
			switch($cmd[0]) {
				case "a":
					$this->set_target_altitude($cmd[1]);
					$this->to_redis();
					if($this->altitude < $this->target_altitude) {
						$response['msg'] = "Will climb to ".$this->target_altitude." feet";
					} elseif($this->altitude > $this->target_altitude) {
						$response['msg'] = "Will descend to ".$this->target_altitude." feet";
					} else {
						$response['msg'] = "Will maintain ".$this->target_altitude." feet";
					}
					$response['class'] = "ok";
					break;
				default:
					throw new Exception("Unknown command");
			}
		} catch(Exception $e) {
			$response['msg'] = "Unable: ".$e->getMessage();
		}
		return $response;
	}
}
