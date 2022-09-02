<?php

$sql = 'DELETE FROM `history` WHERE `user_key` = :user';
$params = [
	':user' => $user_key
];
$db->t_query($sql, $params);

$sql = 'DELETE FROM `user` WHERE `user_key` = :user';
$db->t_query($sql, $params);