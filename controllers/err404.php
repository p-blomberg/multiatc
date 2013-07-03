<?php
/*
Copyright 2011, 2012 Ombetro Handelsbolag
*/

class err404Controller extends ErrorController {
	protected $code = 404;

	public function index() {
		$this->title = "404 Filen hittades inte";
		$this->body = $this->view("/err/404.php");
	}
}

