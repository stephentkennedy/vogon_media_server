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
	
	$mime = mime_content_type($f);
	if(stristr($mime, 'video') === false){
		//If we're not a video, we don't need to do any of the rest.
		continue;
	}
	
	$sql = 'SELECT * FROM data WHERE data_content = :content AND ( data_type = "video" OR data_type = "tv" )';
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
			array_pop($name);
			$name = implode('.', $name);
			$thumb_name = $thumbDir.$name.'.jpg';
			
			try{
				$vid->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))->save($thumb_name);
			} catch(Exception $e){
				echo 'Exception when generating thumbnail<br>'.$e->getMessage().'<br>Continuing without thumbnail.';
				$thumb_name = '';
			}
			$vid = null;
			
			$file_info = $getID3->analyze($f);
			$length = $file_info['playtime_seconds'];
			
			/*
			Name: Stephen Kennedy
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
			debug_d($type);
			
			$record_data = [
				'name' => $name[0],
				'slug' => slugify($name),
				'content' => $f,
				'type' => $type,
				'parent' => $parent
			];
			
			
			/*
			Name: Stephen Kennedy
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
				debug_d($clerk->db->error);
			}
		}
	}
}