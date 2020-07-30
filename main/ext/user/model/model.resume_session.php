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
		/*
		Name: Stephen Kennedy
		Date: 7/28/2020
		Comment: Consider re-examining this. It makes sense because you don't want someone on one device messing with the activity of someone else who is actively watching, but with the logic as it is currently, all that will happen is it will ask the new device to start a new session and then kick the currently watching device out.
		
		There's some kind of compromise between the two, and it's possible that it's just letting two different devices use two different sessions under the same user, even if that might potentially mess up things like watch history.
		*/
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
		//Under normal circumstances, this limit is 45 minutes, but that logic is centered around business systems.
		//This system is for media consumption, and may have a lot of passive behavior from the user.
		//That said, watching videos and the like should generate a lot of passive activity which will re-up the timer
		//But we don't want the timer expiring on the user so we've boosted this to something insane.
		$compare_to = strtotime('twelve hours ago');
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