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
		->hset('aircraft:SAS123', 'target_altitude', 4000)
		->hset('aircraft:SAS123', 'heading', 0)
		->hset('aircraft:SAS123', 'target_heading', 90)
		->hset('aircraft:SAS123', 'speed', 180)
		->hset('aircraft:SAS123', 'target_speed', 180)
		->sadd('aircraft_flying', 'SAS123')
		->exec();

	echo "Server started.\n";
} else {
	echo "Picking up where we left off...\n";
}

$last_tick = microtime(true);
while(true) {
	$this_tick = microtime(true);
	$dt = $this_tick - $last_tick;
	$last_tick = $this_tick;

	$flying = $redis->smembers('aircraft_flying');
	foreach($flying as $flightno) {
		$aircraft = Aircraft::from_redis($flightno);
		$aircraft->tick($dt);
		$aircraft->to_redis();
	}

	usleep(1000000); // 1 second
}
