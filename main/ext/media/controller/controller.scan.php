<?php
//Imports take a bit. If we were C programmers, we'd probably do this with a dedicated programmer, but since we're PHP hacks here we go.
set_time_limit(0);

//Create our classes, in this case $ffmpeg, $getID3, $fs, and $clerk
require ROOT . '/vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();

require_once(ROOT . DIRECTORY_SEPARATOR .  'main'. DIRECTORY_SEPARATOR . 'ext'. DIRECTORY_SEPARATOR . 'audio'. DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'getid3'. DIRECTORY_SEPARATOR . 'getid3.php');
$getID3 = new getID3;
$getID3->setOption(['encoding' => 'UTF-8']);

load_class('filesystem');
$fs = new filesystem;

$clerk = new clerk;

//Scan our files
$files = $fs->recursiveScan($dir, true);

$thumbDir = $_SESSION['thumb_dir'];
if(!file_exists($thumbDir)){
	$thumbDir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs';
	if(!file_exists($thumbDir)){
		mkdir($thumbDir);
	}
}
$thumbDir .= DIRECTORY_SEPARATOR;

if(!empty($series_name) && empty($series_id)){
	$parent = $clerk->addRecord([
		'name' => $series_name,
		'type' => 'series'
	]);
}else if(!empty($series_id)){
	$parent = $series_id;
}else{
	$parent = 0;
}

foreach($files as $f){
	debug_d($f);
	
	$sql = 'SELECT * FROM data WHERE data_content = :content AND data_type = "video"';
	$params = [':content' => $f];
	$query = $db->query($sql, $params);
	if($query == false){
		debug_d($db->error);
	}else{
		$result = $query->fetch();
		if($result == false){
			$vid = $ffmpeg->open($f);
			$name = explode(DIRECTORY_SEPARATOR, $f);
			$name = array_pop($name);
			$name = explode('.', $name);
			$thumb_name = $thumbDir.'DB_'.$name[0].'.jpg';
			
			$vid->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))->save($thumb_name);
			$vid = null;
			
			$record_data = [
				'name' => $name[0],
				'slug' => slugify($name[0]),
				'content' => $f,
				'type' => 'video',
				'parent' => $parent
			];
			
			$file_info = $getID3->analyze($f);
			$length = $file_info['playtime_seconds'];
			
			$meta_data = [
				'poster' => $thumb_name,
				'length' => $length
			];
			
			$clerk->addRecord($record_data, $meta_data);
		}
	}
}