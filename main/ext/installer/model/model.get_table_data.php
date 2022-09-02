<?php

$settings = parse_ini_file(ROOT . DIRECTORY_SEPARATOR . 'main'. DIRECTORY_SEPARATOR . 'config.ini', true);
$db_name = $settings['database']['name'];
$sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = :name';
$params = [
	':name' => $db_name
];

$query = $db->t_query($sql, $params);
if($query == false){
	debug_d($db->error);
	die();
}else{
	$results = $query->fetchAll();
	$return = [];
	foreach($results as $r){
		if(substr($r['table_name'],0, strlen('pma__')) == 'pma__'){
			//We don't export phpmyadmin tables.
			continue;
		}
		$return[] = $r['table_name'];
	}
	asort($return);
	return $return;
}