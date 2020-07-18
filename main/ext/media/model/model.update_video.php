<?php
$clerk = new clerk;
if($genre != false){
	$record_data = [
		'name' => $title,
		'slug' => slugify($title),
		'type' => 'video',
		'parent' => $genre,
		'content' => $location
	];
}else{
	$record_data = [
		'name' => $title,
		'slug' => slugify($title),
		'type' => 'tv',
		'content' => $location
	];
}
$clerk->updateRecord($record_data, $id);
$metas = [
	'director' => $director,
	'release' => $release,
	'starring' => $starring,
	'desc' => $desc,
	'poster' => $poster
];
$clerk->updateMetas($id, $metas);