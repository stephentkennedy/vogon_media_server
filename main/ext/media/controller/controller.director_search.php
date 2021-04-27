<?php

$search = $_GET['director'];

$sql = 'SELECT * FROM `data_meta` WHERE `data_meta_name` = "director" AND `data_meta_content` LIKE :star';

$params = [
	':star' => '%'.$search.'%'
];

$results = $db->query($sql, $params)->fetchAll();

if(count($results) > 1){
	$sql = 'SELECT * FROM `data` WHERE FIND_IN_SET(`data_id`, :ids) ORDER BY `data_name`, LENGTH(`data_name`)';
}else{
	$sql = 'SELECT * FROM `data` WHERE `data_id` = :ids ORDER BY `data_name`, LENGTH(`data_name`)';
}
$temp_array = [];

foreach($results as $r){
	$temp_array[] = $r['data_id'];
}

$params = [
	':ids' => implode(',', $temp_array)
];

$query = $db->query($sql, $params);
if($query == false){
	debug_d($db->error);
}

$final_results = $query->fetchAll();

$string = '';

foreach($final_results as $r){
	$string .= '<a class="button" href="'.build_slug('view/'.$r['data_id'], [], 'media').'">'.$r['data_name'].'</a><br>';
}

echo $string;