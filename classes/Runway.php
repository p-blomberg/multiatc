<?php
class Runway {
	private $length;
	private $forward_heading;
	private $forward_name;
	private $start_point;

	public function __get($property) {
		switch($property) {
			case "backward_heading":
				$hdg = $this->forward_heading - 180;
				if($hdg < 0) {
					$hdg += 360;
				}
				return $hdg;
			case "backward_name":
				$name = substr($this->forward_name, 0, 2) - 18;
				if($name < 0) {
					$name += 36;
				}
				if(strlen($this->forward_name) > 2) {
					switch(substr($this->forward_name, 2, 1)) {
						case 'L':
							$name .= 'R';
							break;
						case 'R':
							$name .= 'L';
							break;
						case 'C':
							$name .= 'C';
					}
				}
				return $name;
			case "begin_x":
				return $this->start_point[0];
			case "begin_y":
				return $this->start_point[1];
			default:
				return $this->$property;
		}
	}

	public function __construct($forward_name, $forward_heading, $start_point, $length) {
		$this->length = $length;
		$this->forward_heading = $forward_heading;
		$this->forward_name = $forward_name;
		$this->start_point = $start_point;
	}
}
