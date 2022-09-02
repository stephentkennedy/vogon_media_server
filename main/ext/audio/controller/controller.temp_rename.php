<?php
$sql = 'SELECT * FROM data WHERE data_content LIKE "%dresden files%"';
$query = $db->t_query($sql, []);
$items = $query->fetchAll();
$clerk = new clerk;

foreach($items as $item){
	
	//Isolate just the number from the filename
	$filename = $item['data_content'];
	$filename = explode('/', $filename);
	$filename = array_pop($filename);
	$filename = explode('.', $filename);
	$filename = $filename[0];
	$filename = explode (' - ', $filename);
	$filename = array_pop($filename);
	
	$title = 'Chapter '.$filename;
	$track = $filename;
	$data = ['name' => $title];
	$meta = ['track' => $track];
	
	$clerk->updateRecord($data, $item['data_id']);
	$clerk->updateMetas($item['data_id'], $meta);
	//echo $title.'<br>';
}