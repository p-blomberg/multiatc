<?php
class Aircraft {
	private $flightno;
	private $model;
	private $location;
	private $altitude;
	private $heading;
	private $speed;

	public function __get($property) {
		switch($property) {
			default:
				return $this->$property;
		}
	}

	public function __construct($flightno, $model, $location, $altitude, $heading, $speed) {
		$this->flightno = $flightno;
		$this->model = $model;
		$this->location = $location;
		$this->altitude = $altitude;
		$this->heading = $heading;
		$this->speed = $speed;
	}
}
