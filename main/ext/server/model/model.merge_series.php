<?php
$clerk = new clerk;
$base = $clerk->getRecord($id);
if(empty($base) || $base['data_type'] != 'series'){
	return;
}

$inner_sql = 'SELECT data_id FROM data WHERE data_type = "series" AND data_name = :name AND data_id != :id';
$params = [
	':name' => $base['data_name'],
	':id' => $base['data_id']
];

$outer_sql = 'UPDATE data SET data_parent = :id WHERE data_parent IN('.$inner_sql.')';

$check = $db->query($outer_sql, $params);
if(empty($check)){
	debug_d($db->error);
	die();
}

$outer_sql = 'DELETE FROM data WHERE data_id IN('.$inner_sql.')';
$db->query($outer_sql, $params);