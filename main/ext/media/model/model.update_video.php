<?php
$clerk = new clerk;
$existing = $clerk->getRecord($id, true);
if($existing['data_type'] == 'video'){
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

if(empty($existing['meta']['length'])){
	require ROOT . '/vendor/autoload.php';
	$ffprobe = FFMpeg\FFProbe::create();
	$length = $ffprobe->format($location)->get('duration');
	if(empty($length)){
		$length = $runtime * 60;
	}
}else{
	$length = $existing['meta']['length'];
}

$metas = [
	'director' => $director,
	'release' => $release,
	'starring' => $starring,
	'desc' => $desc,
	'poster' => $poster,
	'length' => $length
];
$clerk->updateMetas($id, $metas);