<?php
set_time_limit(0);


require ROOT . '/vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();

load_class('filesystem');
$fs = new filesystem;

//$files = $fs->recursiveScan(ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'DBGT', true);
$files = $fs->recursiveScan($dir, true);

$thumbDir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR;

$clerk = new clerk;
//$parent = 10844; //DBZ
//$parent = 11676; //DB
//$parent = 11836; //DBGT
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
			
			$meta_data = [
				'poster' => $thumb_name
			];
			
			$clerk->addRecord($record_data, $meta_data);
		}
	}
}