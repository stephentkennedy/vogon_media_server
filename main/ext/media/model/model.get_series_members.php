<?php
global $user;
load_class('db_handler');
$d = new db_handler('data');
$m = new db_handler('data_meta');
$dm = $d->meta_link($m, [
	'meta_key_field' => 'data_meta_name',
	'meta_value_field' => 'data_meta_content'
]);

$search = [
	'parent' => $id,
	'type' => [
		'tv',
		'video'
	],
	'meta' => [
		'season',
		'poster',
		'episode_ord',
		'length',
		'release',
		'director',
		'desc',
		'starring',
		'series',
	],
	'orderby' => 'meta_episode_ord',
	'orderby_int' => true
];

$episodes = $dm->getRecords($search);

$search_2 = [
	'parent' => $id,
	'type' => 'season',
	'meta' => [
		'season_ord'
	],
	'orderby' => 'meta_season_ord',
	'orderby_int' => true
];

$seasons = $dm->getRecords($search_2);
$season_key = [];
foreach($seasons as $ord => $s){
	$season_key[$s['data_id']] = $ord;
	$seasons[$ord]['episodes'] = [];
}
$loose = [];
foreach($episodes as $e){
	if(!empty($e['season'])){
		$key = $season_key[$e['season']];
		$seasons[$key]['episodes'][] = $e;
	}else{
		$loose[] = $e;
	}
}

return [
	'seasons' => $seasons,
	'loose' => $loose
];