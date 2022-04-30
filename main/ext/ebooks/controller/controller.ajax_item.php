<?php 
$id = get_slug_part(3);
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
		'author',
		'sub_series',
    ],
    'self_join' => [
		'index_field' => 'data_parent',
		'fields' => [
			'data_name',
            'data_id'
		]
	]
];

$record = $dwm->getRecord($search);
if(!empty($record['parent_data_id'])){
    $record['series_link'] = build_slug('series/'.$record['parent_data_id'], [], 'ebooks');
}
if($record['data_type'] == 'pdf'){
	$record['icon'] = 'file-pdf-o';
}else{
	$record['icon'] = 'newspaper-o';
}
echo load_view('json', $record);