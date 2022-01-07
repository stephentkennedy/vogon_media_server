<?php
	$sql = 'SELECT * FROM user WHERE user_email = :email';
	$params = [
		':email' => $email
	];
	$query = $db->query($sql, $params);
	$user_data = $query->fetch();
	$user_data['user_role_mods'] = json_decode($user_data['user_role_mods'], true);
	$email_data = load_model('fetch_user_email', [], 'email');
	$user_data = array_merge($user_data, $email_data);
	
	return $user_data;
?>