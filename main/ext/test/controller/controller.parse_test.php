<?php
global $user;
$page = 0;
$rpp = 25;
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
load_class('search_token_parse');
$parser = new search_token_parse;

$string = 'parent_data_name:"Green Lantern" meta_sub_series:"Blackest Night", meta_sub_series:"Prelude to Blackest Night", green arrow';

$token_array = $parser->parse($string);

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

debug_d($parser->token_array_to_sub_queries($token_array, $default_columns, $all_columns));