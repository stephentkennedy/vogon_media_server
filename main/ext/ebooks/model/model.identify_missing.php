<?php
load_class('db_handler');
$hand = new db_handler('data');
$meta = new db_handler('data_meta');
$hwm = $hand->meta_link($meta, [
	'meta_key_field' => 'data_meta_name',
	'meta_value_field' => 'data_meta_content'
]);

$rpp = 25;
if(empty($page)){
    $page = 1;
}

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
		'author',
		'sub_series',
		'order'
	],
	'type' => ['cbz'],
	'orderby' => ['parent_data_name', 'meta_sub_series', 'meta_order + 0', 'name'],
	'limit' => $rpp,
	'offset' => (($page - 1) * $rpp)
];

$search_options['data_name'] = '';

$results = $hwm->getRecords($search_options);
if(empty($hwm->db->error)){
	return [
		'count' => $hwm->total_count,
		'search_results' => $results,
		'error' => false
	];
}else{
	return [
		'error' => true,
		'message' => $hwm->db->error
	];
}