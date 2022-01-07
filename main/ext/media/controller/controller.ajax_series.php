<?php

$sql = 'SELECT * FROM data WHERE data_type = "series" AND data_name LIKE :search ORDER BY data_name LIMIT 10';
$params = [
	':search' => '%'.$_GET['search'].'%'
];
$query = $db->query($sql, $params);
$results = $query->fetchAll();
$results = json_encode($results);
header('Content-Type: application/json;charset=utf-8');
echo $results;