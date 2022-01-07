<?php
$clerk = new clerk;
$meta_data = [
	'artist' => $meta_artist,
	'genre' => $meta_genre,
	'composer' => $meta_composer,
	'year' => $meta_year,
	'track' => $meta_track
];
$clerk->updateMetas($id, $meta_data);

$sql = 'SELECT * FROM data WHERE data_type = "album" AND data_name = :search ORDER BY data_id ASC LIMIT 1';
$params = [
	':search' => $meta_album
];
$query = $db->query($sql, $params);
$check = $query->fetch();
if(empty($check)){
	$album_id = $clerk->addRecord([
		'name' => $meta_album,
		'type' => 'album'
	]);
}else{
	$album_id = $check['data_id'];
}
$clerk->updateRecord(['name' => $data_name, 'parent' => $album_id], $id);