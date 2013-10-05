<?php
class towerUpdateController extends Controller {
	public function index() {
		global $settings, $redis;
		$flying = $redis->smembers('aircraft_flying');
		foreach($flying as $flightno) {
			$aircraft[] = Aircraft::from_redis($flightno);
		}
		$passalong = array(
			"aircraft" => $aircraft,
		);
		$this->body = $this->view("/tower/tower_update.php", $passalong);
	}
	public function output() {
		return $this->json_output();
	}
}
