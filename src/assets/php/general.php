<?php
	function is_ajax() {
		global $msg;
	
		$msg = "request: " .  $_SERVER['HTTP_X_REQUESTED_WITH'];
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	function validateDate($date) {
	    $d = DateTime::createFromFormat('d-m-Y', $date);
	    return $d && $d->format('d-m-Y') == $date;
	}

	function returnJsonError($origin, $errno, $message) {
		$error = new stdClass();
		$error->error = "error";
		$error->origin = $origin;
		$error->errno = $errno;
		$error->message = $message;
		header('Content-Type: application/json');
		echo json_encode($error);
		exit();
	}
?>