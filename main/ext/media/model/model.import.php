<?php
//Setup
set_time_limit(0);
error_reporting(0);
ini_set('display_errors', false);

require ROOT . '/vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();

require_once(ROOT . DIRECTORY_SEPARATOR .  'main'. DIRECTORY_SEPARATOR . 'ext'. DIRECTORY_SEPARATOR . 'audio'. DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'getid3'. DIRECTORY_SEPARATOR . 'getid3.php');
$getID3 = new getID3;
$getID3->setOption(['encoding' => 'UTF-8']);

$clerk = new clerk;

$thumbDir = $_SESSION['thumb_dir'];
if(!file_exists($thumbDir)){
	$thumbDir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs';
	if(!file_exists($thumbDir)){
		mkdir($thumbDir);
	}
}
$thumbDir .= DIRECTORY_SEPARATOR;

if(!empty($_GET['series_name'])){
	$series_name = $_GET['series_name'];
}
if(!empty($_GET['series_id'])){
	$series_id = $_GET['series_id'];
}else if (!empty($series_name)){
	/*
	Name: Steph Kennedy
	Date: 10/17/21
	Comment: This logic is inside a loop, now we need to account for the possibility that an earlier branch of this loop already created the series.
	*/
	$check = $clerk->getRecord(['name' => $series_name, 'type' => 'series']);
	if(!empty($check)){
		$series_id = $check['data_id'];
	}
}

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

//Import process
$temp = explode('/', $f);
$name = array_pop($temp);

$message = 'Checking "'.$name.'"<br>';

//Mime check
$mime = mime_content_type($f);
if(stristr($mime, 'video') === false){
	$message .= 'File is not a known video type.<br>Skipping.';
	return $message;
}

//Database check.
$sql = 'SELECT * FROM data WHERE data_content = :content AND ( data_type = "video" OR data_type = "tv" )';
$params = [':content' => $f];
$query = $db->t_query($sql, $params);
if($query == false){
	ob_start();
	debug_d($db->error);
	return $message . ob_get_clean();
}
$result = $query->fetch();
if($result != false){
	$message .= 'File already in database.<br>Skipping.';
	return $message;
}

//Beginning Import
$vid = $ffmpeg->open($f);
$name = explode(DIRECTORY_SEPARATOR, $f);
$name = array_pop($name);
$name = explode('.', $name);
array_pop($name);
$name = implode('.', $name);
$thumb_name = $thumbDir.$name.'.jpg';

//Thumbnail Generation
try{
	$vid->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(120))->save($thumb_name);
} catch(Exception $e){
	$message .= 'Exception when generating thumbnail<br>'.$e->getMessage().'<br>Continuing without thumbnail.<br>';
	$thumb_name = '';
}
unset($vid);

//Get the length of our file.
$file_info = $getID3->analyze($f);
if(!empty($file_info['playtime_seconds'])){
	$length = $file_info['playtime_seconds'];
}else{
	$length = 0;
}
/*
Name: Steph Kennedy
Date: 9/22/2020
Comment: To make things a little easier, we're going to attempt to automatically categorize files based on their length
*/
if($length == false){ //This should only happen if the file is too large to read with ID3, which should only happen with movies.
	$type = 'video';
}else if($length > 3600){ //If the file is longer than an hour
	$type = 'video';
}else{
	$type = 'tv';
}
$message .= 'Classified as "'.$type.'"<br>';

$message .= 'Adding to database.';
$record_data = [
	'name' => $name,
	'slug' => slugify($name),
	'content' => $f,
	'type' => $type,
	'parent' => $parent
];
/*
Name: Steph Kennedy
Date: 9/22/2020
Comment: We can support subtitles upon import if they are named the same as the video, but with a text extension. The format itself is in a little bit of flux so we can't guarantee the extension it will use, so instead we'll look for a text mimetype on the file itself and assume. It will be up to the user to ensure the format is correct.

Maybe we'll add something a little more mature in later, like using FFMpeg to generate subtitle files from the subtitles built into the video, but everything has to start somewhere.
*/
$sub_check = explode('.', $f);
$ext = array_pop($sub_check);
$sub_check = implode('.', $sub_check);
$matches = glob($sub_check.'.*');
$subtitles = false;
if(count($matches) > 1){
	foreach($matches as $m){
		$ext_check = explode('.', $m);
		$c_ext = array_pop($ext_check);
		if($c_ext != $ext){
			$mime = mime_content_type($m);
			if(stristr($mime, 'text') !== false){
				$subtitles = $m;
			}
		}
	}
}

$meta_data = [
	'poster' => $thumb_name,
	'length' => $length
];

if($subtitles != false){
	$meta_data['subtitles'] = $subtitles;
}

$check = $clerk->addRecord($record_data, $meta_data);
if($check == false){
	ob_start();
	debug_d($clerk->db->error);
	$message .= ob_get_clean();
}
return $message;