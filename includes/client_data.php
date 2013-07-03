<?php
function clean($data) {
	if(HTML_ACCESS) {
		if(is_array($data)) {
			foreach($data as $key => $value) {
				$data[$key] = clean($value);
			}
			return $data;
		} else {
			return htmlspecialchars($data, ENT_QUOTES, 'utf-8');
		}
	} else {
		return $data;
	}
}
function unclean($data) {
	if(HTML_ACCESS) {
		if(is_array($data)) {
			foreach($data as $key => $value) {
				$data[$key] = unclean($value);
			}
			return $data;
		} else {
			return htmlspecialchars_decode($data, ENT_QUOTES);
		}
	} else {
		return $data;
	}
}
		
function request_get($string) {
	if(isset($_REQUEST[$string])) {
		return clean($_REQUEST[$string]);
	}
	return false;
}
function post_get($string) {
	if(isset($_POST[$string])) {
		return clean($_POST[$string]);
	}
	return false;
}

function session_get($args) {
	if(!is_array($args)){
		$args = func_get_args();
	} 
	$ret = $_SESSION;
	foreach($args as $arg){
		if(!isset($ret[$arg])){
			return false;
		}
		$ret = $ret[$arg];
	}
	return clean($ret);
}

function session_set($string,$value) {
	$_SESSION[$string]=$value;
}

function cookie_set($name, $value) {
	// Expiry: 1 year
	$value = base64_encode($value);
	setcookie($name, $value, time()+31556926, '/');
}

function cookie_get($name) {
	if(isset($_COOKIE[$name])){
		return base64_decode($_COOKIE[$name]);
	} else {
		return false;
	}
}
