<?php
	
	$session_key = session_id();
	
	$_SESSION = [];

	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	
	//Remove the session from our authorized sessions.
	$sql = 'DELETE FROM `session` WHERE `session_key` = :key';
	$params = [':key' => $session_key];
	$db->query($sql, $params);
	
	if(session_status() == PHP_SESSION_ACTIVE){ //PHP 7 will kill session once $_SESSION array is emptied, so this handler will check if the session is already killed
		session_destroy();
	}

?>