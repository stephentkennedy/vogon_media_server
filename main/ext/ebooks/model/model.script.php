<?php

redirect('/'.URI);
die();


$search = [
    'type' => 'cbz',
    'search_content' => '%One Piece%',
    'orderby' => 'data_content',
    //'limit' => 10
];

load_class('db_handler');

$d = new db_handler('data');

$results = $d->getRecords($search);

foreach($results as $order => $record){

    $name = 'One Piece Chapter ';

    $file_name = explode('/', $record['data_content']);
    $file_name = array_pop($file_name);
    $file_name = explode('.', $file_name)[0];
    $name .= $file_name;


    $model_data = [
        'id' => $record['data_id'],
        'author' => 'Eiichiro Oda',
        'year' => '',
        'sub_series' => '',
        'order' => $order,
        'series' => 'One Piece',
        'data_name' => $name
    ];

    load_model('edit_item', $model_data, 'ebooks');
}