<div id="history_div">
	<ul id="history_ul">
		<li>Welcome to Chicago O'Hare. Please try not to break anything.</li>
	</ul>
</div>
<div id="input_div">
	<input type="text" name="input" id="input_input" placeholder="Type your command">
</div>

<script type="text/javascript">
$('input_input').observe('keyup', function(e) {
	var key = e.which || e.keyCode;
	switch(key) {
		default:
			break;
		case 13:
			if($F(this).length < 1) {
				break;
			}
			send_command($F(this));
			this.setValue('');
			break;
	}
});

function send_command(command) {
	$('history_ul').insert({
		bottom: "<li>&gt; " + command + "</li>"
	});
	$('history_div').scrollTop = $('history_div').scrollHeight;
	new Ajax.Request("/PilotChat/send", { onSuccess: function(response) {
			$('history_ul').insert({
				bottom: response.responseText
			});
			$('history_div').scrollTop = $('history_div').scrollHeight;
		}, onFailure: function(response) {
			console.warning("Send command failed: "+response.status);
		}
	});
}
</script>
