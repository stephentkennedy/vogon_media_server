<?php
global $user;
load_class('db_handler');
$hand = new db_handler('data');
$meta = new db_handler('data_meta');
$history = new db_handler('history');
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
		'author',
		'sub_series',
		'order'
	],
	'type' => ['pdf', 'cbz'],
	'orderby' => ['parent_data_name', 'meta_sub_series', 'meta_order + 0', 'name'],
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
	$search = [
		'user_key' => $user['user_key']
	];
	foreach($search_results as $key => $result){
		$search['data_id'] = $result['data_id'];
		$history_val = $history->getRecord($search);
		if(!empty($history_val)){
			$search_results[$key]['history'] = $history_val['history_val'];
		}else{
			$search_results[$key]['history'] = 0;
		}
	}


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
