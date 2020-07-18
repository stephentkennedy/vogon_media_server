<?php
error_reporting(0);
ini_set('display_errors', 0);
$id = get_slug_part(3);
$clerk = new clerk;
$track = $clerk->getRecord($id, true);
$src = str_replace(ROOT, '', $track['data_content']);
$src = str_replace(DIRECTORY_SEPARATOR, '/', $src);
$mime = mime_content_type($track['data_content']);
$title = $track['data_name'];
if(empty($title)){
	$temp = explode('/', $src);
	$title = array_pop($temp);
}
$return = [
	'title' => $title,
	'src' => $src,
	'mime' => $mime,
	'duration' => $track['meta']['length'],
	'artist' => $track['meta']['artist']
];
$return = json_encode($return);
header('Content-Type: application/json;charset=utf-8');
echo $return;