<?php
	$session_id = session_id();
	$sql = 'SELECT * FROM `session` WHERE `session_key` = :session';
	$params = [
		':session' => $session_id
	];
	$query = $db->query($sql, $params);
	if($query == false){
		return false;
	}else{
		return true;
	}
?>