<?php
$id = $_GET['id'];
global $user;

$sql = 'SELECT * FROM `history` WHERE `user_key` = :user AND `data_id` = :id';
$params = [
	':user' => $user['user_key'],
	':id' => $id
];
$query = $db->query($sql, $params);
if($query != false){
	$result = $query->fetch();
	if($result != false){
		$return = [
			'watched' => (float)$result['history_val']
		];
	}else{
		$return = [
			'watched' => 0
		];
	}
}else{
	$return = [
		'watched' => 0
	];
}
header('Content-Type: application/json;charset=utf-8');
echo json_encode($return);