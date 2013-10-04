<?php
$response = array();

$b747 = new AircraftModel();
$aircraft = array(
	new Aircraft("SAS123", $b747, array(-88.1, 41.969), 4000, 90, 180),
	new Aircraft("BA345", $b747, array(-87.85, 41.984), 2000, 270, 160),
);

$response['aircraft'] = $aircraft;

echo json_encode($response);
