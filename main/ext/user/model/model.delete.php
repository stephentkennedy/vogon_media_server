<?php
	$sql = 'DELETE * FROM `user` WHERE `user_key` = :id';
	$params = [
		':id' => $user_key
	];
	return $db->t_query($sql, $params);
?>