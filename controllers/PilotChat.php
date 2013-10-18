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
		$cmd = post_get("command");
		if(empty($cmd)) {
			throw new HTTPError500();
		}
		$response = $this->parse($cmd);
		$this->body = "<li class=\"{$response['class']}\">{$response['msg']}</li>";
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

	private function parse($cmd) {
		$words = explode(' ', $cmd);
		$response = array('class' => '', 'msg' => 'Message not set');

		$first = array_shift($words);
		switch($first) {
			case "help":
				$response['msg'] = $this->helpmsg();
				break;
			default:
				$a = Aircraft::from_redis(strtoupper($first));
				if(empty($a)) {
					$response['class']='error';
					$response['msg']="Aircraft {$first} not found";
					break;
				}
				$response = $a->chat_response($words);
				break;
		}
		return $response;
	}

	private function helpmsg() {
		// @todo: "Kopiera lite crap från task (nitroxy) bara"
		return $this->view('/pilotchat/help.php');
	}
}
