<?php
if(!isset($uri)){
	$uri = $_SERVER['REQUEST_URI'];
}

$sql = 'SELECT * FROM `cache` WHERE `cache_uri` = :uri';
$params = [
	':uri' => $uri
];
$query = $db->query($sql, $params);
if($query != false){
	$result = $query->fetch();
	if($result != false){
		return json_decode($result['cache_content'], true);
	}else{
		return false;
	}
}else{
	return false;
}