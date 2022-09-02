<?php

$clerk = new clerk;
global $user;

$return = load_model('cache_read', []);
if($return == false){

	//Unordered episodes that are part of a series
	$sql = 'SELECT * FROM data WHERE data_parent = :id AND data_type = "tv" AND data_id NOT IN(SELECT data_id FROM data_meta WHERE data_meta_name = "season") ORDER BY data_name';
	$params = [
		':id' => $id
	];
	$query = $db->t_query($sql, $params);
	$tv = $query->fetchAll();
	foreach($tv as $key => $v){		
		$metas = $clerk->getMetas($v['data_id']);
		if(!empty($metas['season'])){
			//We would subdivide into seasons here if we had that data available.
		}else{
			$tv[$key]['meta'] = $metas;
		}
	}


	/*
	Name: Steph Kennedy
	Date: 8/5/2020
	Comment: Previously we were doing a lot of this manually, and avoiding more than one join at a time.
	
	On one hand it makes the code a little easier to read and modify, but on the other it means to get the full metadata we need to make one additional query per item, which is horrifically inefficient. For this reason, I added meta field joins to the Clerk class so that our code can be readable maybe even more so than previously, while still cleaning up the number of queries we do here.
	*/

	//Episodes that are organized into seasons
	$seasons = [];

	$sql = 'SELECT * FROM data, data_meta WHERE data.data_type = "season" AND data.data_parent = :id  AND data.data_id = data_meta.data_id AND data_meta.data_meta_name = "season_ord" ORDER BY data_meta.data_meta_content + 0 ASC';
	$params = [
		':id' => $id
	];
	$query = $db->t_query($sql, $params);
	if($query === false){
		debug_d($sql);
		debug_d($db->error);die();
	}
	$pos_seasons = $query->fetchAll();
	$season_ids = [];
	foreach($pos_seasons as $ord => $s){
		$metas = [
			'season',
			'episode_ord',
			'poster',
			'length',
			'release',
			'director',
			'desc',
			'starring'
		];
		$options = [
			'metas' => $metas,
			'parent' => $id,
			'search_meta' => [
				'season' => $s['data_id']
			],
			'search_meta_mode' => 'strict',
			'orderby' => 'episode_ord + 0'
		];
		
		$episodes = $clerk->getRecords($options);
		
		$seasons[$ord] = [
			'name' => $s['data_name'],
			'episodes' => $episodes
		];
	}


	//Videos and films that are in a series
	$metas = [
		'length',
		'poster',
		'release',
		'director',
		'desc',
		'starring'
	];
	$options = [
		'metas' => $metas,
		'type' => 'video',
		'parent' => $id,
		'orderby' => 'data_name'
	];
	$movies = $clerk->getRecords($options);

	$return = [
		'tv' => $tv,
		'seasons' => $seasons,
		'movies' => $movies
	];
	
	load_model('cache_write', [
		'cache' => $return
	]);
}


return $return;