<?php
set_time_limit(0);
require "includes.php";
ob_end_flush();

$options=getopt('r');

mt_srand(time());

if($redis->get("game_state") === null || array_key_exists('r',$options)) {
	// Clean up redis, just to be sure
	$redis->flushall();

	// Start a new game
	$redis->set("game_state", "running");

	// Create airports
	Airport::spawn();

	// Create some aircraft (should probably happen when clients choose their airspaces)
	$a = Aircraft::random_flight();
	$a->to_redis();
	$redis->sadd('aircraft_flying', $a->flightno);
	echo $a->flightno." spawned in (".$a->location[0].','.$a->location[1].")\n";

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

	if(rand(0,10)==5) { // 10% chance == about every 10 ticks.. this is quite a lot
		$a = Aircraft::random_flight();
		$a->to_redis();
		$redis->sadd('aircraft_flying', $a->flightno);
		echo $a->flightno." spawned in (".$a->location[0].','.$a->location[1].")\n";
	}

	usleep(1000000); // 1 second
}
