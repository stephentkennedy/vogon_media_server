<?php
global $user;
$clerk = new clerk;

if(empty($user) || empty($user['user_key'])){
	return [
		'success' => false,
		'error' => 'No User'
	];
}

if(empty($limit)){
	$limit = 10;
}

/*
Name: Steph Kennedy
Date: 10/22/21
Comment: This is a complex join of a meta_join_db_handler and a direct_link_db_handler. It looks a little gross, but it looks a lot less gross than the original.
*/
load_class('db_handler');
$d = new db_handler('data');
$m = new db_handler('data_meta');
$h = new db_handler('history');
$dm = $d->meta_link($m, [
	'meta_key_field' => 'data_meta_name',
	'meta_value_field' => 'data_meta_content'
]);
$dmh = $dm->direct_link($h);

$search = [
	'link_user_key' => $user['user_key'],
	'greater_than_link_last_edit' => db_date('-30 days'),
	'meta' => [
		'poster'
	],
	'orderby' => 'link_last_edit',
	'orderby_dir' => 'desc',
];
$results = $dmh->getRecords($search);
if(!empty($results)){
	$ordered = [];
	foreach($results as $r){
		if(
			empty($ordered[$r['data_parent']]) 
			&& $r['data_type'] != 'video'
		){
			$r['parent'] = $clerk->getRecord($r['data_parent']);
			$ordered[$r['data_parent']] = $r;
			
		}else if ($r['data_type'] == 'video'){
			$ordered[] = $r;
		}
	}
	
	$results = array_slice(array_values($ordered), 0, $limit);
	return [
		'recent' => $results,
		'success' => true
	];
}else{
	return [
		'success' => false,
		'error' => 'No history within last 30 days'
	];
}