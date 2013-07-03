<?php
/*
Copyright 2013 Petter Blomberg
*/

$HTML_ACCESS = true;
require "../includes.php";

// Prepare path
$path_info=isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
$untouched_request=$path_info;
$request=explode('/',$path_info);
array_shift($request);

$main = array_shift($request);
if(empty($main)) {
	$main = "start";
}
$simple_main=$main;
$main="../controllers/".basename($main).".php";
if(!file_exists($main)) {
	$main="../controllers/err404.php";
	$simple_main="err404";
}

require $main;

try {
	$controllername = UCFirst($simple_main).'Controller';
	$controller = new $controllername();
} catch(HTTPRedirect $e ){
	header("Location: {$e->url}");
	exit();
} catch(Exception $e) {
	if(get_class($e) == "HTTPError") {
		$code = $e->getCode();
	} else {
		$code = 500;
	}
	ob_clean();
	require "../controllers/err{$code}.php";
	$cname = 'err'.$code.'Controller';
	$controller = new $cname($e);
}

echo $controller->output();
