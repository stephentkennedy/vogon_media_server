<?php
$sql = 'SELECT * FROM `data_meta` WHERE `data_meta_id` = :id && `data_meta_name` = "artist"';
$params = [':id' => $id];
$query = $db->t_query($sql, $params);

if($query == false){
	debug_d($db->error);
	return;
}

$artist = $query->fetch()['data_meta_content'];

$sql = 'SELECT * FROM `data_meta` WHERE `data_meta_name` = "artist" AND `data_meta_content` LIKE :search';
$params = [
	':search' => $artist
];

$query = $db->t_query($sql, $params);

if($query == false){
	debug_d($db->error);
	return;
}

$ids = [];
$results = $query->fetchAll();
foreach($results as $r){
	$ids[] = $r['data_id'];
}

/* Select Albums */
$sql = 'SELECT data_parent FROM `data` WHERE `data_type` = "audio" AND `data_parent` != 0 AND FIND_IN_SET(`data_id`, :ids) GROUP BY `data_parent`';
$params = [
	':ids' => implode(',', $ids)
];
$query = $db->t_query($sql, $params);

if($query == false){
	debug_d($db->error);
	return;
}

$albums_find = $query->fetchAll();

$album_ids = [];

foreach($albums_find as $a){
	$album_ids[] = $a['data_parent'];
}

$sql = 'SELECT * FROM `data` WHERE `data_type` = "album" AND FIND_IN_SET(`data_id`, :ids) ORDER BY `data_name`';
$params = [
	':ids' => implode(',', $album_ids)
];

$query = $db->t_query($sql, $params);

if($query == false){
	debug_d($db->error);
	return;
}

$albums = $query->fetchAll();

/* Select Loose Songs */
$sql = 'SELECT data_parent FROM `data` WHERE `data_type` = "audio" AND `data_parent` = 0 AND FIND_IN_SET(`data_id`, :ids)';
$params = [
	':ids' => implode(',', $ids)
];
$query = $db->t_query($sql, $params);

if($query == false){
	debug_d($db->error);
	return;
}

$loose_songs = $query->fetchAll();

return [
	'artist' => $artist,
	'albums' => $albums,
	'loose_songs' => $loose_songs
];