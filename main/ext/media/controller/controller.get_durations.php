<?php

set_time_limit(0);
require_once(ROOT . DIRECTORY_SEPARATOR .  'main'. DIRECTORY_SEPARATOR . 'ext'. DIRECTORY_SEPARATOR . 'audio'. DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'getid3'. DIRECTORY_SEPARATOR . 'getid3.php');
$getID3 = new getID3;
$getID3->setOption(['encoding' => 'UTF-8']);
$clerk = new clerk;

//$sql = 'SELECT * FROM data WHERE data_type = "video" OR data_type = "tv"';
$sql = 'SELECT * FROM data WHERE data_parent = 12698 AND data_type = "tv"';
$query = $db->query($sql, []);
$items = $query->fetchAll();
foreach($items as $item){
	//$file_info = $getID3->analyze($f);
	//$length = $file_info['playtime_seconds'];
	$meta = $clerk->getMetas($item['data_id']);
	if(empty($meta['length'])){
		$file = $item['data_content'];
		if(stristr($file, ROOT) === false){
			$file = ROOT . $file;
		}
		$file_info = $getID3->analyze($file);
		$length = $file_info['playtime_seconds'];
		$clerk->addMetas($item['data_id'], ['length' => $length]);
		debug_d(['file' => $file, 'length' => $length]);
	}
}