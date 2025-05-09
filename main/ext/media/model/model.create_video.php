<?php

$thumbDir = $_SESSION['thumb_dir'];
if(!file_exists($thumbDir)){
	$thumbDir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs';
	if(!file_exists($thumbDir)){
		mkdir($thumbDir);
	}
}
$thumbDir .= DIRECTORY_SEPARATOR;

$clerk = new clerk;
$record_data = [
	'name' => $title,
	'slug' => slugify($title),
	'type' => 'video',
	'parent' => 0,
	'content' => $location
];

require ROOT . '/vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();

require_once(ROOT . DIRECTORY_SEPARATOR .  'main'. DIRECTORY_SEPARATOR . 'ext'. DIRECTORY_SEPARATOR . 'audio'. DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'getid3'. DIRECTORY_SEPARATOR . 'getid3.php');
$getID3 = new getID3;
$getID3->setOption(['encoding' => 'UTF-8']);

$vid = $ffmpeg->open($location);
$name = explode(DIRECTORY_SEPARATOR, $location);
$name = array_pop($name);
$name = explode('.', $name);
$thumb_name = $thumbDir.'DB_'.$name[0].'.jpg';

try{
	@$vid->frame(@FFMpeg\Coordinate\TimeCode::fromSeconds(120))->save($thumb_name);
} catch(Exception $e){
	$message .= 'Exception when generating thumbnail<br>'.$e->getMessage().'<br>Continuing without thumbnail.<br>';
	$thumb_name = '';
}
unset($vid);

$file_info = $getID3->analyze($location);

if(isset($file_info['playtime_seconds'])){
	$length = $file_info['playtime_seconds'];
}else{
	
	$ffprobe = FFMpeg\FFProbe::create();
	$length = $ffprobe->format($location)->get('duration');
	if(empty($length)){
		$length = $runtime * 60;
	}
}

$metas = [
	'director' => $director,
	'release' => $release,
	'starring' => $starring,
	'desc' => $desc,
	'poster' => $thumb_name,
	'length' => $length,
	'animorphic' => $animorphic
];
return $clerk->addRecord($record_data, $metas);
