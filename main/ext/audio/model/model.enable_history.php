<?php
$clerk = new clerk;
$clerk->updateMetas($id, ['history' => true]);

$sql = 'SELECT `data_id` FROM `data` WHERE `data_parent` = :id';
$params = [
	':id' => $id
];
$query = $db->query($sql, $params);
$items = $query->fetchAll();

foreach($items as $i){
	$clerk->updateMetas($i['data_id'], ['history' => true]);
}