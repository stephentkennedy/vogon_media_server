<?php
	$sql = 'SELECT * FROM `user` WHERE `user_key` = :id';
	$params = [
		':id' => $user_key
	];
	$query = $db->query($sql, $params);
	$user_data = $query->fetch();
	$user_data['user_role_mods'] = json_decode($user_data['user_role_mods'], true);
	return $user_data;
?>