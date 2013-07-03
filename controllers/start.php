<?php
class startController extends Controller {
	public function index() {
		global $settings;
		$this->title = "Startsida för ".$settings['app_name'];
		$this->body = $this->view("/start/start.php");
	}
}
