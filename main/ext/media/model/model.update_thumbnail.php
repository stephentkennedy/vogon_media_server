<?php
//Setup

require ROOT . '/vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();

$clerk = new clerk;
$message = '';

$thumbDir = $_SESSION['thumb_dir'];
if(!file_exists($thumbDir)){
	if(empty($thumbDir)){
		$thumbDir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs';
	}
	if(!file_exists($thumbDir)){
		mkdir($thumbDir);
	}
}
$thumbDir .= DIRECTORY_SEPARATOR;

$item = $clerk->getRecord($id, true);

$old_file = $item['meta']['poster'];
if(!empty($old_file)){
	unlink($old_file);
}

$timestamp = floor($seconds);

$vid_file = $item['data_content'];

if(stristr($vid_file, ROOT) === -1){
	$vid_file = ROOT . DIRECTORY_SEPARATOR . $vid_file;
}

$vid = $ffmpeg->open($vid_file);
$name = explode(DIRECTORY_SEPARATOR, $vid_file);
$name = array_pop($name);
$name = explode('.', $name);
array_pop($name);
$name = implode('.', $name);
$name = slugify($name);
$thumb_name = $thumbDir.$name.'.jpg';
try{
	$vid->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($timestamp))->save($thumb_name);
	$clerk->updateMetas($id, [
		'poster' => $thumb_name
	]);
} catch(Exception $e){
	$message .= 'Exception when generating thumbnail<br>'.$e->getMessage().'<br>Continuing without thumbnail.<br>';
	$thumb_name = '';
	$clerk->updateMetas($id, [
		'poster' => $thumb_name
	]);
}

unset($vid);

$thumb_url = build_slug(str_replace(ROOT, '', $thumb_name));

return [
	'message' => $message,
	'thumbnail' => $thumb_url
];