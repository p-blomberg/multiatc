<?php
set_time_limit(0);
require "includes.php";
ob_end_flush();

$options=getopt('r');

if($redis->get("game_state") === null || array_key_exists('r',$options)) {
	// Clean up redis, just to be sure
	$redis->delete('aircraft');
	$redis->delete('aircraft_flying');

	// Start a new game
	$redis->set("game_state", "running");

	// Create some aircraft (should probably happen when clients choose their airspaces)
	$redis->multi()
		->hset('aircraft:SAS123', 'model', 'Boeing 747')
		->hset('aircraft:SAS123', 'location_x', -88.1)
		->hset('aircraft:SAS123', 'location_y', 41.969)
		->hset('aircraft:SAS123', 'altitude', 4000)
		->hset('aircraft:SAS123', 'heading', 90)
		->hset('aircraft:SAS123', 'speed', 180)
		->sadd('aircraft_flying', 'SAS123')
		->exec();

	echo "Server started.\n";
} else {
	echo "Picking up where we left off...\n";
}

while(true) {
	$flying = $redis->smembers('aircraft_flying');
	foreach($flying as $flightno) {
		$aircraft = Aircraft::from_redis($flightno);
		$aircraft->tick();
		$aircraft->to_redis();
	}

	usleep(1000000); // 1 second
}
