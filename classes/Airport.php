<?php
class Airport {
	private $center_point;
	private $airspace_range;
	private $runways;

	public function __construct($center_point, $airspace_range) {
		$this->center_point = $center_point;
		$this->airspace_range = $airspace_range;
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
}
