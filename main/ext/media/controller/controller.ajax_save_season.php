<?php
set_time_limit(0);
$seasons = $_POST['season'];
foreach($seasons as $key => $s){
	$name = $s['name'];
	$ep = $s['episodes'];
	$season_obj = [
		'order' => $key,
		'name' => $name,
		'episodes' => $ep
	];
	load_model('save_season', $season_obj, 'media');
}
load_model('clean_unused_seasons', [
	'count' => count($seasons), 
	'test' => $seasons[0]['episodes'][0]
], 'media');
echo 'Changes made.';