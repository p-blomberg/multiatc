<?php
class Aircraft implements JsonSerializable {
	private $flightno;
	private $model;
	private $location;
	private $altitude;
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
			"heading" => $this->heading,
			"speed" => $this->speed,
		);
	}

	public function __construct($flightno, $model, $location, $altitude, $heading, $speed) {
		$this->fields = array('flightno','model','location','altitude','heading','speed');
		foreach($this->fields as $f) {
			$this->$f = $$f;
		}
	}

	public static function from_redis($flightno) {
		global $redis;
		$data = $redis->hgetall('aircraft:'.$flightno);
		$location = array($data['location_x'], $data['location_y']);
		return new Aircraft($flightno, $data['model'], $location, $data['altitude'], $data['heading'], $data['speed']);
	}

	public function to_redis() {
		global $redis;
		$redis->multi()
			->hset('aircraft:'.$this->flightno, 'model', $this->model)
			->hset('aircraft:'.$this->flightno, 'location_x', $this->location[0])
			->hset('aircraft:'.$this->flightno, 'location_y', $this->location[1])
			->hset('aircraft:'.$this->flightno, 'altitude', $this->altitude)
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
	}
}
