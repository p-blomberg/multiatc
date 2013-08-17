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

	console.info(dt);

	t.fillStyle="#000022";
	t.fillRect(0,0,w,h);
	t.fillStyle="#FF0033";
	//t.fillRect(w*0.1,h*0.1,w*0.8,h*0.8);

	t.font = "bold 12px sans-serif";
	/*
	t.fillText("x", 248, 43);
	t.fillText("y", 58, 165);
	*/

	runways();
}

function runways() {
	t.fillStyle="#999";
	t.font = "normal 12px sans-serif";
	
	<?php
	$ord = new Airport(array(41.979492, -87.905597), 30*1852);
	$ord->add_runway(new Runway("10", "093", array(-87.9315, 41.969), 3962));

	$runway_length = array( // meters
		"4L/22R" => 2286,
		"4R/22L" => 2461,
		"9L/27R" => 2286,
		"9R/27L" => 2428,
		"10L/28R" => 3962,
		"14L/32R" => 3050,
		"14R/32L" => 2952,
	);

	$airspace_begin_x = -89;
	$airspace_begin_y = 42.5;
	$airspace_end_x = -87;
	$airspace_end_y = 41.5;
	$airspace_width = $airspace_end_x - $airspace_begin_x;
	$airspace_height = $airspace_end_y - $airspace_begin_y;

	$runway_begin_x = -87.9315;
	$runway_end_x = -87.884;
	$runway_begin_y = 41.969;
	$runway_end_y = 41.969;

	foreach($ord->runways as $rw) {
		echo 't.fillText("'.$runway_end_x.' - '.$rw->begin_x.' = '.($runway_end_x - $rw->begin_x).'", 20, 20);';
		echo "t.fillRect( 
			w * ".(($rw->begin_x - $airspace_begin_x) / $airspace_width).",
			h * ".(($runway_begin_y - $airspace_begin_y) / $airspace_height).",
			w * ".(($runway_end_x - $rw->begin_x) / $airspace_width).",
			2);";
		echo "t.fillText(
			'".$rw->forward_name."',
			w * ".(($rw->begin_x - $airspace_begin_x) / $airspace_width)." -t.measureText(".$rw->forward_name.").width-10,
			h * ".(($runway_begin_y - $airspace_begin_y) / $airspace_height)."+5);";
		echo "t.fillText(
			'".$rw->backward_name."',
			w * ".(($rw->begin_x - $airspace_begin_x) / $airspace_width)." + 
			w * ".(($runway_end_x - $rw->begin_x) / $airspace_width)." +10,
			h * ".(($runway_begin_y - $airspace_begin_y) / $airspace_height)."+5);";
	}
	?>
}
</script> 
