<?php
class Controller {
	protected $title, $body;

	function view($view, $data=array()){
		global $path_view, $this_user, $settings, $debug;
		extract($data);
		ob_start();
		require($path_view.$view);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function route($path){
		$func = 'index';
		if ( count($path) > 0){
			$func = array_shift($path);
			if($func == '') {
				$func = 'index';
			}
		}

		$func = array($this, $func);
		if ( !is_callable($func) ){
			throw new HTTPError404();
		}

		return call_user_func_array($func, array($path));
	}

	public function __construct() {
		global $request;
		return $this->route($request);
	}

	public function http_headers() {}
	public function html_head_extras() { return ''; }

	public function title() {
		return $this->title;
	}
	public function body() {
		return $this->body;
	}

	public function raw_output() {
		$this->http_headers();
		return $this->body;
	}

	public function json_output() {
		header('Content-type: application/json');
		return $this->raw_output();
	}

	public function html_output() {
		$this->http_headers();
		$data['title'] = $this->title();
		$data['head_extras'] = $this->html_head_extras();
		echo $this->view("html_head.php", $data);
		echo $this->body();
		echo $this->view("html_footer.php", $data);
	}

	public function output() {
		return $this->html_output();
	}
}

class HTTPError extends Exception {
  public $code;

  public function __construct($code, $message){
    parent::__construct($message);
    $this->code = $code;
  }
}

class HTTPError403 extends HTTPError {
  public function __construct($message=null){
    parent::__construct(403, $message);
  }
}

class HTTPError404 extends HTTPError {
  public function __construct($message=null) {
    parent::__construct(404, $message);
  }
}

class HTTPRedirect extends Exception {
  public $url;

  public function __construct($url, $raw_url=null){
    parent::__construct();
		if($raw_url !== null) {
			$this->url = $raw_url;
		} else {
			$this->url = base_path().$url;
		}
  }
}

class ErrorController extends Controller {
	protected $exception;
	protected $code = 500;

	public function __construct($exception = null) {
		global $this_user, $untouched_request;

		$this->exception = $exception;

		if($exception != null) {
			trigger_error(get_class($this)." constructed. Request: $untouched_request, User: ".$this_user->id.", exception message: ".$exception->getMessage().", trace: ".$exception->getTraceAsString(), E_USER_WARNING );
		} else {
			trigger_error(get_class($this)." constructed. Request: $untouched_request, User: ".($this_user===false?'not logged in':$this_user->id).", no exception.", E_USER_WARNING );
		}

		return parent::__construct();
	}

	public function route($path) {
		return $this->index();
	}

	public function httpheaders() {
		header("HTTP/1.0 ".$this->code);
	}
}
