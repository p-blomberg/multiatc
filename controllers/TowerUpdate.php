<?php
class towerUpdateController extends Controller {
	public function index() {
		global $settings;
		$this->body = $this->view("/tower/tower_update.php");
	}
	public function output() {
		return $this->json_output();
	}
}
