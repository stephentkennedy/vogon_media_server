<?php

$clerk = new clerk;

$sql = 'SELECT * FROM data WHERE data_parent = :id AND data_type = "tv" AND data_id NOT IN(SELECT data_id FROM data_meta WHERE data_meta_name = "season") ORDER BY data_name';
$params = [':id' => $id];
$query = $db->query($sql, $params);
$tv = $query->fetchAll();
foreach($tv as $key => $v){
	
	$metas = $clerk->getMetas($v['data_id']);
	if(!empty($metas['season'])){
		//We would subdivide into seasons here if we had that data available.
	}else{
		$tv[$key]['meta'] = $metas;
	}
}

$seasons = [];

$sql = 'SELECT * FROM data, data_meta WHERE data.data_type = "season" AND data.data_parent = :id  AND data.data_id = data_meta.data_id AND data_meta.data_meta_name = "season_ord" ORDER BY data_meta.data_meta_content + 0 ASC';
$params = [
	':id' => $id
];
$query = $db->query($sql, $params);
if($query === false){
	debug_d($sql);
	debug_d($db->error);die();
}
$pos_seasons = $query->fetchAll();
$season_ids = [];
foreach($pos_seasons as $ord => $s){
	$sql = 'SELECT * FROM data, data_meta WHERE data.data_id = data_meta.data_id AND data.data_parent = :id AND data.data_id IN(SELECT data_id FROM data_meta WHERE data_meta_name = "season" AND data_meta_content = :season) AND data_meta.data_meta_name = "episode_ord" ORDER BY data_meta.data_meta_content + 0 ASC';
	$params = [
		':id' => $id,
		':season' => $s['data_id']
	];
	$query = $db->query($sql, $params);
	$episodes = $query->fetchAll();
	$seasons[$ord] = [
		'name' => $s['data_name'],
		'episodes' => $episodes
	];
}



$sql = 'SELECT * FROM data WHERE data_parent = :id AND data_type = "video" ORDER BY data_name';
$params = [
	':id' => $id
];
$query2 = $db->query($sql, $params);
if($query2 !== false){
	$movies = $query2->fetchAll();
}else{
	$movies = [];
}

foreach($movies as $key => $v){
	$metas = $clerk->getMetas($v['data_id']);
	$movies[$key]['metas'] = $metas;
}

return [
	'tv' => $tv,
	'seasons' => $seasons,
	'movies' => $movies
];