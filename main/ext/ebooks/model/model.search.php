<?php
global $user;
load_class('db_handler');
$hand = new db_handler('data');
$meta = new db_handler('data_meta');
$hwm = $hand->meta_link($meta, [
	'meta_key_field' => 'data_meta_name',
	'meta_value_field' => 'data_meta_content'
]);
$search_options = [
	'self_join' => [
		'index_field' => 'data_parent',
		'fields' => [
			'data_name'
		]
	],
	'meta' => [
		'year',
		'genre',
		'author'
	],
	'type' => ['pdf', 'cbz', 'cbr'],
	'orderby' => 'name',
	'limit' => $rpp,
	'offset' => (($page - 1) * $rpp)
];
if(!empty($search)){
	$search_options['sub_query'][] = [
		'query_mode' => 'OR',
		'search_parent_data_name' => '%'.$search.'%',
		'search_meta_artist' => '%'.$search.'%',
		'search_name' => '%'.$search.'%',
		'search_content' => '%'.$search.'%',
	];
}
$search_results = $hwm->getRecords($search_options);
if(empty($hwm->db->error)){
	return [
		'count' => $hwm->total_count,
		'search_results' => $search_results,
		'error' => false
	];
}else{
	return [
		'error' => true,
		'message' => $hwm->db->error
	];
}
