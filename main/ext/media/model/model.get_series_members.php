<?php

$clerk = new clerk;
global $user;

//Unordered episodes that are part of a series
$sql = 'SELECT * FROM data WHERE data_parent = :id AND data_type = "tv" AND data_id NOT IN(SELECT data_id FROM data_meta WHERE data_meta_name = "season") ORDER BY data_name';
$params = [
	':id' => $id
];
$query = $db->query($sql, $params);
$tv = $query->fetchAll();
foreach($tv as $key => $v){
	
	//Get history data
	$sql = 'SELECT * FROM history WHERE data_id = :id AND user_key = :user';
	$params = [
		':id' => $v['data_id'],
		':user' => $user['user_key']
	];
	$query = $db->query($sql, $params);
	$history = $query->fetch();
	if(!empty($history)){
		$tv[$key]['time'] = $history['history_val'];
	}else{
		$tv[$key]['time'] = false;
	}
	
	
	$metas = $clerk->getMetas($v['data_id']);
	if(!empty($metas['season'])){
		//We would subdivide into seasons here if we had that data available.
	}else{
		$tv[$key]['meta'] = $metas;
	}
}


//Episodes that are organized into seasons
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
	$sql = 'SELECT * FROM data, data_meta WHERE data.data_id = data_meta.data_id 
	AND data.data_parent = :id 
	AND data.data_id IN(SELECT data_id FROM data_meta WHERE data_meta_name = "season" AND data_meta_content = :season) AND data_meta.data_meta_name = "episode_ord" 
	ORDER BY data_meta.data_meta_content + 0 ASC';
	$params = [
		':id' => $id,
		':season' => $s['data_id'],
	];
	$query = $db->query($sql, $params);
	$episodes = $query->fetchAll();
	
	foreach($episodes as $key => $e){
		$sql = 'SELECT * FROM history WHERE data_id = :id AND user_key = :user';
		$params = [
			':id' => $e['data_id'],
			':user' => $user['user_key']
		];
		$query = $db->query($sql, $params);
		$history = $query->fetch();
		if(!empty($history)){
			$episodes[$key]['time'] = $history['history_val'];
		}else{
			$episodes[$key]['time'] = false;
		}
		$meta = $clerk->getMetas($e['data_id']);
		$episodes[$key]['meta'] = $meta;
	}
	
	$seasons[$ord] = [
		'name' => $s['data_name'],
		'episodes' => $episodes
	];
}


//Videos and films that are in a series

$sql = 'SELECT * FROM data WHERE data.data_parent = :id AND data.data_type = "video" ORDER BY data.data_name';
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
	
	$sql = 'SELECT * FROM history WHERE data_id = :id AND user_key = :user';
	$params = [
		':id' => $v['data_id'],
		':user' => $user['user_key']
	];
	$query = $db->query($sql, $params);
	$history = $query->fetch();
	if(!empty($history)){
		$movies[$key]['time'] = $history['history_val'];
	}else{
		$movies[$key]['time'] = false;
	}
	
	$metas = $clerk->getMetas($v['data_id']);
	$movies[$key]['meta'] = $metas;
}

return [
	'tv' => $tv,
	'seasons' => $seasons,
	'movies' => $movies
];