<canvas id="tower" width="100" height="100"></canvas> 
<script>
var t_canvas=document.getElementById("tower");
var t=t_canvas.getContext("2d");
var w=100, h=100;
var last_main = null;

function resize() {
	_w = w; _h = h;
	w = window.innerWidth;
	h = window.innerHeight;
	console.info("Resize from (" + _w + ", " + _h + ") to ("  + w + ", " + h + ")");
	t_canvas.width = w;
	t_canvas.height = h;
}

resize();

Event.observe(window, 'resize', resize);
window.setInterval(main, 100);

function main() {
	if(last_main == null) {
		last_main = new Date().getTime();
	}
	now = new Date().getTime();
	dt = now - last_main;
	last_main = now;

	//console.info(dt);

	t.fillStyle="#000022";
	t.fillRect(0,0,w,h);
	t.fillStyle="#FF0033";

	t.font = "bold 12px sans-serif";

	draw_runways();
	draw_aircraft();
}

function draw_runways() {
	t.fillStyle="#999";
	t.strokeStyle="#999";
	t.font = "normal 10px sans-serif";
	
	<?php
	$ord = new Airport(array(41.979492, -87.905597), 30*1852);
	$ord->add_runway(new Runway("10", "093", array(-87.9315, 41.969, -87.8837, 41.969), 13001));
	$ord->add_runway(new Runway("14L", "143", array(-87.91533, 42.0025, -87.891667, 41.981333), 10005));
	$ord->add_runway(new Runway("14R", "143", array(-87.933167, 41.9905, -87.910167, 41.97), 9685));
	$ord->add_runway(new Runway("4R", "045", array(-87.8995, 41.953333, -87.879667, 41.97), 8075));
	$ord->add_runway(new Runway("4L", "042", array(-87.914, 41.981667, -87.896333, 41.9975), 7500));
	$ord->add_runway(new Runway("9R", "093", array(-87.918333, 41.983833, -87.889, 41.983833), 7967));
	$ord->add_runway(new Runway("9L", "093", array(-87.926667, 42.002833, -87.899167, 42.002833), 7500));

	$airspace_begin_x = -88.2;
	$airspace_begin_y = 42.1;
	$airspace_end_x = -87.6;
	$airspace_end_y = 41.8;
	$airspace_width = $airspace_end_x - $airspace_begin_x;
	$airspace_height = $airspace_end_y - $airspace_begin_y;

	foreach($ord->runways as $rw) {
		echo "t.beginPath();";
		echo "t.moveTo(
			w * ".(($rw->begin_x - $airspace_begin_x) / $airspace_width).",
			h * ".(($rw->begin_y - $airspace_begin_y) / $airspace_height).");";
		echo "t.lineTo(
			w * ".(($rw->end_x - $airspace_begin_x) / $airspace_width).",
			h * ".(($rw->end_y - $airspace_begin_y) / $airspace_height).");";
		echo "t.stroke();";

		echo "t.fillText(
			'".$rw->forward_name."',
			(w * ".(($rw->begin_x - $airspace_begin_x) / $airspace_width).") -t.measureText('".$rw->forward_name."').width-5,
			h * ".((($rw->begin_y - $airspace_begin_y) / $airspace_height) - (($rw->end_y - $rw->begin_y) / $airspace_height*0.2))."+5);";
		echo "t.fillText(
			'".$rw->backward_name."',
			(w * ".(($rw->begin_x - $airspace_begin_x) / $airspace_width)." +
			(w * ".(($rw->end_x - $rw->begin_x) / $airspace_width).")) +10,
			(h * ".((($rw->end_y - $airspace_begin_y) / $airspace_height) + (($rw->end_y - $rw->begin_y) / $airspace_height*0.2)).")+5);";
	}
	?>
}

function draw_aircraft() {
	t.fillStyle="#FFF";
	t.strokeStyle="#FFF";
	t.font = "normal 10px sans-serif";

	<?php
	$airspace_begin_x = -88.2;
	$airspace_begin_y = 42.1;
	$airspace_end_x = -87.6;
	$airspace_end_y = 41.8;
	$airspace_width = $airspace_end_x - $airspace_begin_x;
	$airspace_height = $airspace_end_y - $airspace_begin_y;

	$b747 = new AircraftModel();
	$aircraft = array(
		new Aircraft("SAS123", $b747, array(-88.1, 41.969), 4000, 90, 180),
		new Aircraft("BA345", $b747, array(-87.85, 41.984), 2000, 270, 160),
	);
	foreach($aircraft as $a) {
		echo "t.fillRect(
			(w * ".(($a->location[0] - $airspace_begin_x) / $airspace_width).")-2,
			(h * ".(($a->location[1] - $airspace_begin_y) / $airspace_height).")-2,
			5, 5);";
		echo 't.textBaseline = "top";';
		echo "t.fillText(
			'".$a->flightno."',
			(w * ".(($a->location[0] - $airspace_begin_x) / $airspace_width)."-t.measureText('".$a->flightno."').width/2),
			(h * ".(($a->location[1] - $airspace_begin_y) / $airspace_height).")-20);";
	}
	?>
}
</script> 
