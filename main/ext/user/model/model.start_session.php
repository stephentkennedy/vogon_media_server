<?php
	
	//One session per user please.
	$sql = 'DELETE FROM session WHERE user_key = :key';
	$params = [
		':key' => $user_key
	];
	$db->query($sql, $params);

	$sql = 'INSERT INTO session (user_key, session_key, session_ip, create_date, last_edit) VALUES (:user, :key, :ip, :create, :edit)';
	$params = [
		':user' => $user_key,
		':key' => session_id(),
		':ip' => $_SERVER['REMOTE_ADDR'],
		':create' => date('Y-m-d H:i:s'),
		':edit' => date('Y-m-d H:i:s')
	];

	$return = $db->query($sql, $params);
	
	return $return
?>