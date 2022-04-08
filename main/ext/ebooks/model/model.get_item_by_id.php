<?php 
load_class('db_handler');
$d = new db_handler('data');
$dm = new db_handler('data_meta');
$dwm = $d->meta_link($dm, [
	'meta_key_field' => 'data_meta_name',
	'meta_value_field' => 'data_meta_content'
]);
$search = [
    'id' => $id,
    'meta' => [
        'year',
		'genre',
		'author'
    ],
    'self_join' => [
		'index_field' => 'data_parent',
		'fields' => [
			'data_name'
		]
	]
];

return $dwm->getRecord($search);