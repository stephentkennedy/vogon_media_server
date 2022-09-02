<?php

	if(empty($password)){
		//We're initializing the password later
		$password = null;
		$salt = null;
	}else{
		//Otherwise, hash them before adding them to the database
		$salt = load_model('generate_salt', [], 'user');
		$password = load_model('hash_password', [
			'password' => $password,
			'salt' => $salt
		], 'user');
	}
	if(!empty($role_mods)){
		$role_mods = null;
	}

	$sql = 'INSERT INTO `user` (`user_role`, `user_email`, `user_pass`, `user_salt`, `user_role_mods`) VALUE (:role, :email, :pass, :salt, :mods)';
	$params = [
		':role' => $role,
		':email' => $email,
		':pass' => $password,
		':salt' => $salt,
		':mods' => json_encode($role_mods)
	];
	return $db->t_query($sql, $params);
?>