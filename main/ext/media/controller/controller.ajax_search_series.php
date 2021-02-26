<?php

$sql = 'SELECT data_name FROM data WHERE data_type = "series" AND data_name LIKE :search ORDER BY data_name LIMIT 10';
$params = [
	':search' => '%'.$_GET['search'].'%'
];
$query = $db->query($sql, $params);
$results = $query->fetchAll();
echo load_view('json', $results);