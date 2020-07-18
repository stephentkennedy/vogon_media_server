<?php

$sql = 'SELECT * FROM `data` WHERE `data_type` = "genre" ORDER BY `data_name` ASC';
$genres = $db->query($sql, [])->fetchAll();

if(empty($meta['poster'])){
	$meta['poster'] = '';
}
$temp = $meta['poster'];
$temp = explode('/', $temp);
$temp = array_pop($temp);

$sql = 'SELECT * FROM `data` WHERE `data_type` = "series" AND `data_id` = :id';
$params = [
	':id' => $data_parent
];
$series = $db->query($sql, $params)->fetch();
if($series != false && count($series) > 0){
	$series_id = $series['data_id'];
	$series = $series['data_name'];	
}else{
	$series = '';
	$series_id = 0;
}

$thumb_dir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR;

if(file_exists($thumb_dir.$temp)){
	$thumb = '/upload/thumbs/'.$temp;
}else{
	$thumb = $meta['poster'];
}

if(empty($meta['director'])){
	$meta['director'] = '';
}
if(empty($meta['release'])){
	$meta['release'] = '';
}
if(empty($meta['starring'])){
	$meta['starring'] = '';
}
if(empty($meta['desc'])){
	$meta['desc'] = '';
}
$meta['poster'] = str_replace(ROOT, '', $meta['poster']);

return [
	'title' => $data_name,
	'genre' => $data_parent,
	'genres' => $genres,
	'id' => $data_id,
	'director' => $meta['director'],
	'release' => $meta['release'],
	'starring' => $meta['starring'],
	'desc' => $meta['desc'],
	'location' => str_replace(ROOT, '', $data_content),
	'poster' => $meta['poster'],
	'thumb' => $thumb,
	'series' => $series,
	'series_id' => $series_id
];