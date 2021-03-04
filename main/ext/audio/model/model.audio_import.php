<?php
set_time_limit(0);
require_once(ROOT . DIRECTORY_SEPARATOR .  'main'. DIRECTORY_SEPARATOR . 'ext'. DIRECTORY_SEPARATOR . 'audio'. DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'getid3'. DIRECTORY_SEPARATOR . 'getid3.php');

$getID3 = new getID3;
$getID3->setOption(['encoding' => 'UTF-8']);

$clerk = new clerk;

$mime = mime_content_type($f);
if($mime == 'application/octet-stream'){
	//Sometimes php gets confused about badly formed mime type headers, seems to be most common in .mp3 files so we're going to check for that extension.
	if(substr($f, -4) == '.mp3'){
		$mime = 'audio/mpeg';
	}
}

$name = explode(DIRECTORY_SEPARATOR, $f);
$name = array_pop($name);
$message = 'Checking "'.$name.'"<br>';
$message .= 'Mime: '.$mime.'<br>';



$mime = explode('/', $mime);
if($mime[0] == 'audio' || substr($f, -4) == '.wma'){
		
	$sql = 'SELECT * FROM data WHERE data_content = :content AND data_type = "audio"';
	$params = [':content' => $f];
	$query = $db->query($sql, $params);
	if($query == false){
		ob_start();
		debug_d($db->error);
		return ob_get_clean();
	}else{
		$result = $query->fetch();
		if($result == false){
			if(in_array(substr($f, -4), [
				'.wma',
				'.wav',
				'.aif'
			])){
				$message .= 'Attempting to convert it to MP3 file.<br>';
				//If we're one of the above we'll convert the file.
				require ROOT . '/vendor/autoload.php';

				$ffmpeg = FFMpeg\FFMpeg::create();
				$ffprobe = FFMpeg\FFProbe::create();
				
				$new_name = substr($f, 0, strlen($f) - 4).'.mp3';
				$audio = $ffmpeg->open($f);
				$format = new FFMpeg\Format\Audio\Mp3();
				//Convert the file
				$audio->save($format, $new_name);
				if($ffprobe->isValid($new_name)){
					$message .= 'Conversion successful.<br>';
					unlink($f);
					$f = $new_name;
				}else{
					$message .= 'Unable to convert.<br>';
					unlink($new_name);
				}
			}
			$message .= 'Attempting to read Meta Data.<br>';
			$file_info = $getID3->analyze($f);
			
			$comments = $file_info['id3v2']['comments'];
			$title = $comments['title'][0];
			$track_number = $comments['track_number'][0];
			$album = $comments['album'][0];
			$artist = $comments['band'][0];
			$composer = $comments['composer'][0];
			$year = $comments['year'][0];
			$length = $file_info['playtime_seconds'];
			$genre = $comments['genre'][0];
			
			if(!empty($album)){
				$message .= 'Checking whether to add or use existing album.<br>';
				$sql = 'SELECT * FROM data WHERE data_name = :album AND data_type = "album"';
				$params = [':album' => $album];
				$query = $db->query($sql, $params);
				if($query != false){
					$result = $query->fetch();
				}else{
					$result = false;
				}
				if($result == false){
					$message .= 'Adding album.<br>';
					$album_id = $clerk->addRecord([
						'name' => $album,
						'type' => 'album',
						'slug' => slugify($album)
					]);
				}else{
					$message .= 'Using existing album.<br>';
					$album_id = $result['data_id'];
				}
			}else{
				$album_id = 0;
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
			
			$message .= 'Adding database entry.';
			
			$clerk->addRecord($record_data, $meta_data);
			
		}else{
			$message .= 'File already exists in database.<br>Skipping.';
		}
		return $message;
	}
}else{
	$message .= 'File is not a known audio format.<br>Skipping.';
	return $message;
}