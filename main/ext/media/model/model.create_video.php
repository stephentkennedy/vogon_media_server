<?php
$clerk = new clerk;
$record_data = [
	'name' => $title,
	'slug' => slugify($title),
	'type' => 'video',
	'parent' => $genre,
	'content' => $location
];
$metas = [
	'director' => $director,
	'release' => $release,
	'starring' => $starring,
	'desc' => $desc,
	'poster' => $poster
];
return $clerk->addRecord($record_data, $metas);