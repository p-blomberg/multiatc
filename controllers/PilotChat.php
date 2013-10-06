<?php
class pilotChatController extends Controller {
	private $raw_response;

	public function index() {
		global $settings;
		$this->title = "Pilot Chat";
		$this->body = $this->view("/pilotchat/pilotchat.php");
	}
	public function send() {
		$this->raw_response = true;
		$this->body = "<li>Hej!</li>";
	}
	public function output() {
		if($this->raw_response) {
			return $this->raw_output();
		} else {
			return parent::output();
		}
	}

	public function html_head_extras() {
		return '<link rel="stylesheet" href="/css/game.css">';
	}
}
