<?php

$fish = new fish;
$zip_url = 'https://github.com/stephentkennedy/vogon_media_server/archive/master.zip';

$filename = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update.zip';
$h = fopen($filename, 'w');

$fish->url = $zip_url;
$fish->cOps([
	CURLOPT_FILE => $h, //Write to our file
	CURLOPT_RETURNTRANSFER => 0 //Don't attempt to return the string (it might be too large to keep in memory)
], true);

$fish->dispatch();

if(file_exists($filename)){
	$check = filesize($filename);
	if($check > 0){
		return 'Downloaded update.';
	}
}

$_SESSION['error'] = true;
return 'Download failed.';