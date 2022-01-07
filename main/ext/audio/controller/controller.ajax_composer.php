<?php

$sql = 'SELECT DISTINCT data_meta_content FROM data_meta WHERE data_meta_name = "composer" AND data_meta_content LIKE :search ORDER BY data_meta_content LIMIT 10';
$params = [
	':search' => '%'.$_GET['search'].'%'
];
$query = $db->query($sql, $params);
$results = $query->fetchAll();
$results = json_encode($results);
header('Content-Type: application/json;charset=utf-8');
echo $results;