<?php
/*
Copyright 2011, 2012 Ombetro Handelsbolag
*/

class err500Controller extends ErrorController {
	public function index() {
		$this->title = "Ett oväntat fel uppstod";
		$this->body = $this->view("/err/500.php");
	}
}
