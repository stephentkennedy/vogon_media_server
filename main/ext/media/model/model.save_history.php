<?php
global $user;

$sql = 'SELECT * FROM `history` WHERE `user_key` = :user AND `data_id` = :id';
$params = [
	':user' => $user['user_key'],
	':id' => $id
];
$query = $db->query($sql, $params);
if($query != false){
	$return = $query->fetch();
	if(!empty($return)){
		$sql = 'UPDATE `history` SET `history_val` = :time WHERE `history_id` = :history_id';
		$params = [
			':time' => $time,
			':history_id' => $return['history_id']
		];
	}else{
		$sql = 'INSERT INTO `history` (user_key, data_id, history_val) VALUES (:user, :id, :time)';
		$params = [
			':user' => $user['user_key'],
			':id' => $id,
			':time' => $time
		];
	}
	$db->query($sql, $params);
}
	