<?php
/*
Name: Steph Kennedy
Date: 2/21/21
Comment: Audiophiles might be mad at this choice, but browsers only really understand a limited subset of audio files: .mp3 .ogg .flac

Any common format outside of those needs to be converted. I chose MP3 here because it's the smallest format (but the most lossy hence the audiophile anger). We want the format to be small because we aren't currently doing a size check before doing the conversion so we want the best chance at being able to make our copy of the file to test before we remove the original to save space.

TODO: Add a filespace check before we do this so we can avoid errors where we can't write the file because we don't have enough room (which the error handling might check I'm not entirely sure yet), and then switch this conversion to something less lossy so we're not losing quality in this.
*/
set_time_limit(0);

require ROOT . '/vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();
$ffprobe = FFMpeg\FFProbe::create();

$sql = 'SELECT * FROM data WHERE data_content LIKE "%.wma" OR data_content LIKE "%.wav" OR data_content LIKE "%.aif"';
$query = $db->query($sql, []);

$results = $query->fetchAll();
$i = 1;

load_controller('header', ['title' => 'Audio Conversion']);
echo '<header><h1>Audio Conversion</h1></header>';
foreach($results as $r){
	$f = $r['data_content'];
	/*$new_name = str_replace([
		'.wma',
		'.wav',
		'.aif'
	], '.mp3', $f);*/
	$new_name = substr($f, 0, strlen($f) - 4).'.mp3';
	$audio = $ffmpeg->open($f);
	$format = new FFMpeg\Format\Audio\Mp3();
	
	//Convert the file
	$audio->save($format, $new_name);
	if($ffprobe->isValid($new_name)){
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
	}else{
		unlink($new_name);
		echo $i. '.) Error Converting: '.$f.'<br>';
	}
	$i++;
}
echo '<a class="button" href="'.$_SERVER['HTTP_REFERER'].'">Go Back</a>';
load_controller('footer');