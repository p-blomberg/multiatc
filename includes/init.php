<?php
// Cacha headers och innehåll så att vi kan ändra oss och skicka
// en Location-header även efter att vi börjat eka ut innehåll.
ob_start();

// Init the session
session_start();

/**
 * Automatiskt anropad av php on-demand för att include:a filer med klassdefinitioner.
 * Antar att den globala variabeln $repo_root innehåller sökvägen till svn-repots root-mapp.
 */
function __autoload($class)
{
	global $repo_root;
	if(file_exists($repo_root.'/classes/'.$class.'.php'))
		require_once $repo_root.'/classes/'.$class.'.php';
}

/**
 * Klasser som behöver instantieras till en global.
 */

/* No database needed yet
$db=@new MySQLi($settings['db_host'], $settings['db_user'], $settings['db_password'], $settings['db_name']);
if($db->connect_error) {
	throw new Exception("Unable to connect to database (".$db->connect_error.")");
}
$db->set_charset("utf8");
$db->autocommit(false);
*/

// Connect to Redis
$redis = new Redis;
$redis->connect($settings['redis_host']);
