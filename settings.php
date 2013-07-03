<?php
// DEBUG
$settings['debug']=false;
$debug=$settings['debug'];
ini_set("display_errors", $settings['debug']);

// International stuff
date_default_timezone_set("Europe/Stockholm");
setlocale(LC_NUMERIC, 'en_US.utf8');

// Paths
$repo_root = dirname(__FILE__);
$path_view = $repo_root."/views";

// Other stuff
$settings['app_name']="Synka RSS";

// Database
$settings['db_host']="127.0.0.1";
$settings['db_user']="synkarss";
$settings['db_password']="J7rGCSccZXrQXPcc";
$settings['db_name']="synkarss";
