<?php
set_time_limit(0);

require ROOT . '/vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();

$sql = 'SELECT * FROM data WHERE data_content LIKE "%.wma"';
$query = $db->query($sql, []);

$results = $query->fetchAll();
$i = 1;
foreach($results as $r){
	$f = $r['data_content'];
	$new_name = str_replace('.wma', '.mp3', $f);
	$audio = $ffmpeg->open($f);
	$format = new FFMpeg\Format\Audio\Mp3();
	
	//Convert the file
	$audio->save($format, $new_name);
	
	//Delete the old one
	unlink($f);
	
	$name = explode(DIRECTORY_SEPARATOR, $f);
	$name = array_pop($name);
	$name = str_replace('.wma', '', $name);
	
	$sql = 'UPDATE data SET data_content = :content, data_name = :name WHERE data_id = :id';
	$params = [
		':content' => $new_name,
		':name' => $name,
		':id' => $r['data_id']
	];
	$db->query($sql, $params);
	echo $i. '.) '.$new_name.'<br>';
	$i++;
}