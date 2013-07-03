<?
$this_user = session_get("user");
if(empty($this_user)) {
	$this_user = false;
}

function has_access($right) {
	global $this_user;
	if($this_user === false) {
		return false;
	}
	return $this_user->has_access($right);
}
function require_access($right) {
	if(!has_access($right)) {
		throw new HTTPError403();
	}
}
function require_login() {
	global $this_user;
	if($this_user === false) {
		throw new HTTPError403();
	}
}
