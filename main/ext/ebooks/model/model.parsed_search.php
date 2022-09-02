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
    $default_columns = [
        'parent_data_name',
        'meta_author',
        'meta_sub_series',
        'name',
        'content'
    ];
    $all_columns = [
        'parent_data_name',
        'meta_author',
        'meta_sub_series',
        'name',
        'content',
        'meta_year',
        'order'
    ];
    load_class('search_token_parse');
    $parser = new search_token_parse;
    $tokenized_search = $parser->parse($search);
    $sub_queries = $parser->token_array_to_sub_queries($tokenized_search, $default_columns, $all_columns);
    $search_options['sub_query'] = $sub_queries;
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
