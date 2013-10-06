<?php
class Airport {
	private $iata_code;
	private $location;
	private $runways;

	public function __construct($iata_code, $location) {
		$this->iata_code = $iata_code;
		$this->location = $location;
		$this->runways = array();
	}

	public function add_runway($runway) {
		$this->runways[] = $runway;
	}

	public function __get($property) {
		switch($property) {
			default:
				return $this->$property;
		}
	}

	public static function spawn() {
		$a = new Airport("ORD", array(-87.905597, 41.979492));
		$a->add_runway(new Runway("10", "093", array(-87.9315, 41.969, -87.8837, 41.969), 13001));
		$a->add_runway(new Runway("14L", "143", array(-87.91533, 42.0025, -87.891667, 41.981333), 10005));
		$a->add_runway(new Runway("14R", "143", array(-87.933167, 41.9905, -87.910167, 41.97), 9685));
		$a->add_runway(new Runway("4R", "045", array(-87.8995, 41.953333, -87.879667, 41.97), 8075));
		$a->add_runway(new Runway("4L", "042", array(-87.914, 41.981667, -87.896333, 41.9975), 7500));
		$a->add_runway(new Runway("9R", "093", array(-87.918333, 41.983833, -87.889, 41.983833), 7967));
		$a->add_runway(new Runway("9L", "093", array(-87.926667, 42.002833, -87.899167, 42.002833), 7500));
		$a->to_redis();
	}

	public static function from_redis($iata_code) {
		global $redis;
		$data = $redis->hgetall('airport:'.$iata_code);
		if($data == null) {
			return null;
		}
		$location = array($data['location_x'], $data['location_y']);
		// FIXME: get runways from redis
		return new Airport($iata_code, $location);
	}

	public function to_redis() {
		global $redis;
		$redis->multi()
			->hset('airport:'.$this->iata_code, 'location_x', $this->location[0])
			->hset('airport:'.$this->iata_code, 'location_y', $this->location[1])
			->exec();
		// FIXME: runways should be stored in redis
	}

}
