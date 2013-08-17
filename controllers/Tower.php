<?php
class towerController extends Controller {
	public function index() {
		global $settings;
		$this->title = "ORD Tower";
		$this->body = $this->view("/tower/tower.php");
	}

	public function html_head_extras() {
		return '<link rel="stylesheet" href="/css/game.css">';
	}
}
