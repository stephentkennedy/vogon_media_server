<?php
	$sql = 'SELECT * FROM `user` WHERE `user_email` = :email';
	$params = [':email' => $email];
	$query = $db->t_query($sql, $params);
	if($query == false){
		return [
			'correct' => false,
			'error' => true,
			'reason' => 'no_such_user'
		];
	}else{
		$user_data = $query->fetch();
		$salt = $user_data['user_salt'];
		$check = load_model('hash_password', [
			'password' => $password,
			'salt' => $salt
		], 'user');
		if($check == $user_data['user_pass']){
			return [
				'correct' => true,
				'error' => false,
				'user_key' => $user_data['user_key']
			];
		}else{
			return [
				'correct' => false,
				'error' => false
			];
		}
	}
?>