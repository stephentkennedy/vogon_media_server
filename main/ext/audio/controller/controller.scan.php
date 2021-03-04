<?php

set_time_limit(0);

load_class('filesystem');
$fs = new filesystem;

//Need to build a way to pass variables to the init_model

$files = $fs->recursiveScan($dir, true);

require_once(ROOT . DIRECTORY_SEPARATOR .  'main'. DIRECTORY_SEPARATOR . 'ext'. DIRECTORY_SEPARATOR . 'audio'. DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'getid3'. DIRECTORY_SEPARATOR . 'getid3.php');

$getID3 = new getID3;
$getID3->setOption(['encoding' => 'UTF-8']);

$clerk = new clerk;

//This gets written into the processing models. Add format conversion to the process.
foreach($files as $f){
	$mime = mime_content_type($f);
	$mime = explode('/', $mime);
	if($mime[0] == 'audio' || substr($f, -4) == '.wma'){
		$sql = 'SELECT * FROM data WHERE data_content = :content AND data_type = "audio"';
		$params = [':content' => $f];
		$query = $db->query($sql, $params);
		if($query == false){
			debug_d($db->error);
		}else{
			$result = $query->fetch();
			if($result == false){
				$file_info = $getID3->analyze($f);
				
				$comments = $file_info['id3v2']['comments'];
				//debug_d($comments);
				//die();
				$title = $comments['title'][0];
				$track_number = $comments['track_number'][0];
				$album = $comments['album'][0];
				$artist = $comments['band'][0];
				$composer = $comments['composer'][0];
				$year = $comments['year'][0];
				$length = $file_info['playtime_seconds'];
				$genre = $comments['genre'][0];
				
				$sql = 'SELECT * FROM data WHERE data_name = :album AND data_type = "album"';
				$params = [':album' => $album];
				$query = $db->query($sql, $params);
				if($query != false){
					$result = $query->fetch();
				}else{
					$result = false;
					debug_d($db->error);
				}
				if($result == false){
					$album_id = $clerk->addRecord([
						'name' => $album,
						'type' => 'album',
						'slug' => slugify($album)
					]);
				}else{
					$album_id = $result['data_id'];
				}
				
				$record_data = [
					'name' => $title,
					'slug' => slugify($title),
					'content' => $f,
					'type' => 'audio',
					'parent' => $album_id
				];
				
				$meta_data = [
					'track' => $track_number,
					'artist' => $artist,
					'genre' => $genre,
					'composer' => $composer,
					'year' => $year,
					'length' => $length
				];
				
				$clerk->addRecord($record_data, $meta_data);
				
			}
		}
	}
}