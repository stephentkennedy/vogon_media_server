<?php

$settings = parse_ini_file(ROOT . DIRECTORY_SEPARATOR . 'main'. DIRECTORY_SEPARATOR . 'config.ini', true);
$db_name = $settings['database']['name'];
$sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = :name';
$params = [
	':name' => $db_name
];

$query = $db->query($sql, $params);
if($query == false){
	debug_d($db->error);
	die();
}else{
	$results = $query->fetchAll();
	$return = [];
	foreach($results as $r){
		$return[] = $r['table_name'];
	}
	return $return;
}