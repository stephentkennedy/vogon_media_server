<?php

$sql = 'DELETE FROM `history` WHERE `user_key` = :user';
$params = [
	':user' => $user_key
];
$db->query($sql, $params);

$sql = 'DELETE FROM `user` WHERE `user_key` = :user';
$db->query($sql, $params);