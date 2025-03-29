<?php

$sql = 'SELECT * FROM `data` WHERE `data_type` = "genre" ORDER BY `data_name` ASC';
$genres = $db->t_query($sql, [])->fetchAll();

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
$series = $db->t_query($sql, $params)->fetch();
if($series != false && count($series) > 0){
	$series_id = $series['data_id'];
	$series = $series['data_name'];	
}else{
	$series = '';
	$series_id = 0;
}

/*
Name: Steph Kennedy
Date: 7/25/20
Comment: Fetch the history for this user and this item
*/
global $user;
$sql = 'SELECT * FROM  `history` WHERE `user_key` = :user AND `data_id` = :id';
$params = [
	':user' => $user['user_key'],
	':id' => $data_id
];
$query = $db->t_query($sql, $params);
$result = false;
if($query != false){
	$result = $query->fetch();
}
if(empty($result)){
	$time = 0;
}else{
	$time = $result['history_val'];
}

$thumb_dir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR;

if(!empty($temp) && file_exists($thumb_dir.$temp)){
	$thumb = build_slug('upload/thumbs/').$temp;
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
if(empty($meta['length'])){
	$meta['length'] = 0;
}
if(empty($meta['subtitles']) || substr($meta['subtitles'], -4) == '.srt'){
	//If we haven't set a file manually, look if a vtt file exists in the same directory with the same name.

	$filename_check = $data_content;
	$filename_check = explode('.', $filename_check);
	array_pop($filename_check);
	$filename_check = implode('.', $filename_check);
	$filename_check .= '.vtt';
	if(file_exists($filename_check)){
		$meta['subtitles'] = str_replace(ROOT, '', $filename_check);
	}else{
		$meta['subtitles'] = false;
	}

}

if(!empty($meta['subtitles']) && stristr($meta['subtitles'], ROOT) !== false){
	$meta['subtitles'] = str_replace(ROOT, '', $meta['subtitles']);
}

$meta['poster'] = str_replace(ROOT, '', $meta['poster']);
if(empty($meta['animorphic'])){
	$meta['animorphic'] = 0;
}
if(empty($data_content)){
	$data_content = '';
}
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
	'series_id' => $series_id,
	'time' => $time,
	'length' => $meta['length'],
	'subtitles' => $meta['subtitles'],
	'animorphic' => $meta['animorphic']
];
