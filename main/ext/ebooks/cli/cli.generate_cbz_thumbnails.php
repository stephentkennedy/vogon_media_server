<?php

load_class('db_handler');
$d = new db_handler('data');
$dm = new db_handler('data_meta');
$dwm = $d->meta_link($dm, [
	'meta_key_field' => 'data_meta_name',
	'meta_value_field' => 'data_meta_content'
]);

$rpp = 20;
$offset = 0;

$search = [
    'type' => 'cbz',
    'meta' => ['poster'],
    'meta_poster' => null,
    'limit' => $rpp,
    'offset' => $offset
];

$records = $dwm->getRecords($search);

//$cli->line(json_encode($records, JSON_PRETTY_PRINT));

if(empty($records)){
    $cli->error('No records returned from db search.');
}

$cli->line($dwm->total_count.' records to process');

foreach($records as $item){
    $cli->line('Generating for: '.$item['data_content']);
    $model_data = load_model('create_cbz_thumbnail', [
        'id' => $item['data_id']
    ], 'ebooks');
}

while(
    $offset <= $dwm->total_count
    //&& count($records) == $rpp
){
    $offset += $rpp;
    $search = [
        'type' => 'cbz',
        'meta' => ['poster'],
        'meta_poster' => null,
        'limit' => $rpp,
        'offset' => $offset,
        'orderby' => 'id'
    ];
    
    $records = $dwm->getRecords($search);
    foreach($records as $item){
        $cli->line('Generating for: '.$item['data_content']);
        $model_data = load_model('create_cbz_thumbnail', [
            'id' => $item['data_id']
        ], 'ebooks');
    }
}

$cli->success('End of file');