<?php

$clerk = new clerk;

$record = $clerk->getRecord($test);
$series = $record['data_parent'];

$sql = 'SELECT * FROM data, data_meta WHERE data.data_type = "season" AND data.data_parent = :parent AND data.data_id = data_meta.data_id AND data_meta.data_meta_name = "season_ord" AND data_meta.data_meta_content + 0 >= :ord';
$params = [
	':parent' => $series,
	':ord' => $count
];
$query = $db->t_query($sql, $params);
if(!empty($query)){
	$toDelete = $query->fetchAll();
	foreach($toDelete as $d){
		$clerk->removeRecord($d['data_id']);
	}
}

