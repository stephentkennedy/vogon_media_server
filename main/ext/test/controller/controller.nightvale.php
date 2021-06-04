<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Nightvale Fix<br>';

$sql = 'SELECT * FROM data WHERE data_parent = "16850"';
$query = $db->query($sql);

$results = $query->fetchAll();

$clerk = new clerk;

foreach($results as $r){
	$test = substr(trim($r['data_name']), 0, 1);
	if(is_numeric($test)){
		$temp = explode('-', $r['data_name']);
		$track = trim($temp[0]);
	}else{
		$track = 900;
	}
	$clerk->updateMetas($r['data_id'], [
		'track' => $track
	]);
}