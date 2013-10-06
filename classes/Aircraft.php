<?php
class Aircraft implements JsonSerializable {
	private $flightno;
	private $model;
	private $location;
	private $altitude;
	private $target_altitude;
	private $heading;
	private $target_heading;
	private $speed;
	private $target_speed;
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
			"target_heading" => $this->target_heading,
			"speed" => $this->speed,
			"target_speed" => $this->target_speed,
		);
	}

	public function __construct($flightno, $model, $location, $altitude, $target_altitude, $heading, $target_heading, $speed, $target_speed) {
		$this->fields = array('flightno','model','location','altitude','target_altitude','heading','target_heading','speed','target_speed');
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
		return new Aircraft($flightno, $data['model'], $location, $data['altitude'], $data['target_altitude'], $data['heading'], $data['target_heading'], $data['speed'], $data['target_speed']);
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
			->hset('aircraft:'.$this->flightno, 'target_heading', $this->target_heading)
			->hset('aircraft:'.$this->flightno, 'speed', $this->speed)
			->hset('aircraft:'.$this->flightno, 'target_speed', $this->target_speed)
			->exec();
	}

	public function tick($dt) {
		/* 1 tick = $dt = 1 sekund = 1/60 minut
		 * 1 knop = 1 nm per timme = 1/60 nm per minut
		 */

		// Update location
		$nm = 1/60/60;
		$nm_per_tick = $this->speed * $nm / 60 * $dt;
		$this->location[0] += $nm_per_tick * sin(deg2rad($this->heading));
		$this->location[1] += $nm_per_tick * cos(deg2rad($this->heading));

		// Update altitude
		$max_fps = 2500/60; // feet, not frames
		if($this->target_altitude != $this->altitude) {
			$diff = $this->target_altitude - $this->altitude;
			if($diff > 0) {
				$this->altitude += min($max_fps, $diff);
			} else {
				$this->altitude += max(-$max_fps, $diff);
			}
		}

		// Update speed
		$max_kps = 5; // knots per second
		if($this->target_speed != $this->speed) {
			$diff = $this->target_speed - $this->speed;
			if($diff > 0) {
				$this->speed += min($max_kps, $diff);
			} else {
				$this->speed += max(-$max_kps, $diff);
			}
		}

		// Update heading
		// FIXME: turn left or right to make shortest turn
		// If current heading is 010, and target 350, it now turns 340 degrees right. Wrong!
		$max_dps = 2; // degrees per second
		if($this->target_heading != $this->heading) {
			$diff = $this->target_heading - $this->heading;
			if($diff > 0) {
				$this->heading += min($max_dps, $diff);
			} else {
				$this->heading += max(-$max_dps, $diff);
			}
		}

	}

	private function set_target_altitude($altitude) {
		if(!is_numeric($altitude) || $altitude > 50 || $altitude < 1) {
			throw new Exception("Bad altitude");
		}
		$this->target_altitude = $altitude * 1000;
	}

	private function set_target_speed($speed) {
		if(!is_numeric($speed) || $speed > 500 || $speed < 50) {
			throw new Exception("Bad speed");
		}
		$this->target_speed = $speed;
	}

	private function set_target_heading($heading) {
		if(!is_numeric($heading) || $heading > 360 || $heading < 0) {
			throw new Exception("Bad heading");
		}
		$this->target_heading = $heading;
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
				case "s":
					$this->set_target_speed($cmd[1]);
					$this->to_redis();
					if($this->speed < $this->target_speed) {
						$response['msg'] = "Will accelerate to ".$this->target_speed." knots";
					} elseif($this->speed > $this->target_speed) {
						$response['msg'] = "Will slow down to ".$this->target_speed." knots";
					} else {
						$response['msg'] = "Will maintain ".$this->target_speed." knots";
					}
					$response['class'] = "ok";
					break;
				case "h":
					$this->set_target_heading($cmd[1]);
					$this->to_redis();
					$response['msg'] = "Will turn to heading ".$this->target_heading;
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
