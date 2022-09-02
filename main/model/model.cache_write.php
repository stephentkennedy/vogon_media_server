<?php
if(!isset($uri)){
	$uri = $_SERVER['REQUEST_URI'];
}
/*
Name: Steph Kennedy
Date: 7/29/2020
Comment: This model will be passed in data to save to the cache table in a variable called $cache
*/
$sql = 'SELECT * FROM `cache` WHERE `cache_uri` = :uri';
$params = [
	':uri' => $uri
];
$query = $db->t_query($sql, $params);
if($query != false){
	$result = $query->fetch();
	if($result != false){
		$sql = 'UPDATE `cache` SET `cache_content` = :content WHERE `cache_uri` = :uri';
	}else{
		$sql = 'INSERT INTO `cache` (`cache_uri`, `cache_content`) VALUE (:uri, :content)';
	}
	$params = [
		':content' => json_encode($cache),
		':uri' => $uri
	];
	$db->t_query($sql, $params);
	return true;
}else{
	return false;
}