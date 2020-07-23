<?php
	global $user;
	
	$session_key = session_id();
	
	$sql = 'SELECT * FROM `session` WHERE `session_key` = :key';
	$params = [
		':key' => $session_key
	];
	$query = $db->query($sql, $params);
	if($query == false){
		return [
			'session' => false,
		];
	}else{
		
		$session_data = $query->fetch();
		
		//We don't allow people to take over sessions on different devices
		if($_SERVER['REMOTE_ADDR'] != $session_data['session_ip']){
			return [
				'session' => false
			];
		}
		
		//Get our user from the database
		$user = load_model('get', ['user_key' => $session_data['user_key']], 'user');
		
		$return = [
			'session' => true
		];
		
		$check = strtotime($session_data['last_edit']);
		$compare_to = strtotime('45 minutes ago');
		if($check <= $compare_to){
			$return['reauth'] = true;
		}else{
			$sql = 'UPDATE `session` SET `last_edit` = :date WHERE `session_id` = :id';
			$params = [
				':id' => $session_data['session_id'],
				':date' => date('Y-m-d H:i:s')
			];
			$db->query($sql, $params);
		}
		return $return;
	}
?>