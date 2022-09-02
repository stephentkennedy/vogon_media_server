<?php
global $user;
$sql = 'SELECT * FROM `data` WHERE `data_name` = "email" AND `data_type` = "credentials" AND `data_parent` = :user';
$params = [
	':user' => $user['user_key']
];
$query = $db->t_query($sql, $params);
$db_data = $query->fetch();
if($db_data != false){
	$sql = 'UPDATE `data` SET `data_content` = :content WHERE `data_id` = :id';
	$content['iv'] = base64_encode($content['iv']);
	debug_d(json_encode($content));
	$params = [
		':content' => json_encode($content),
		':id' => $db_data['data_id']
	];
	$query = $db->t_query($sql, $params);
}
else{
	$sql = 'INSERT INTO `data` (data_name, data_slug, data_content, data_type, data_parent, data_status, user_key) VALUES (:name, :slug, :content, :type, :parent, :status, :user)';
	$content['iv'] = base64_encode($content['iv']);
	$params = [
		':name' => 'email',
		':slug' => 'email',
		':content' => json_encode($content),
		':type' => 'credentials',
		':parent' => $user['user_key'],
		':status' => 'active',
		':user' => $user['user_key']
	];
	$query = $db->t_query($sql, $params);
}
return $query;