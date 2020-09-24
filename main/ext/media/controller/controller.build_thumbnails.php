<?php

$thumb_dir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs';

$exist = scandir($thumb_dir);

$clerk = new clerk;
$movies = $clerk->getRecords(['type'=>'tv', 'parent' = 15899], true);

load_class('thumbnailer', 'media');
$th = new thumbnailer;

foreach($movies as $m){
	$p = $m['meta']['poster'];
	$p_array = explode('/', $p);
	$p_name = array_pop($p_array);
	if(!in_array($p_name, $exist)){
		$p_file = str_replace('/', DIRECTORY_SEPARATOR, $p);
		$p_file = ROOT . $p_file;
		$p_new = $thumb_dir . DIRECTORY_SEPARATOR . $p_name;
		
		$th->createThumbnail($p_file, $p_new, 323, 482);
	}
}