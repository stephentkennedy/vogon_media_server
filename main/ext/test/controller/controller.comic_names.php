<?php

echo 'running';

load_class('db_handler');
$d = new db_handler('data');
$dm = new db_handler('data_meta');
$dwm = $d->meta_link($dm, [
    'meta_key_field' => 'data_meta_name',
    'meta_value_field' => 'data_meta_content'
]);

$search = [
    'type' => 'cbz',
    'search_content' => '%Deadpool%',
    'self_join' => [
        'index_field' => 'data_parent',
        'fields' => [
            'data_name'
        ]
    ],
    'meta' => [
        'sub_series'
    ],
    'meta_sub_series' => ['', null],
    'parent_data_name' => ['', null]
];

$results = $dwm->getRecords($search);
$replace = ROOT.'/upload/comics/';


foreach($results as $row){
    $id = $row['data_id'];
    $loc = $row['data_content'];
    $loc = str_replace($replace, '', $loc);
    $array = explode('/', $loc);
    $filename = array_pop($array);
    $series = '';
    if(!empty($array[0])){
        $series = $array[0];
        $s_record = [
            'type' => 'series',
            'name' => $series
        ];
        $parent_id = $d->addRecord($s_record);
        $u_record = [
            'parent' => $parent_id
        ];
        $d->updateRecord($u_record, $id);
    }
    $sub_series = '';
    if(!empty($array[1])){
        $sub_series = $array[1];
        $m_record = [
            'name' => 'sub_series',
            'content' => $sub_series,
            'data_id' => $id
        ];
        $dm->addRecord($m_record);
    }
    debug_d([
        'series' => $series,
        'sub_series' => $sub_series
    ]);
}




/*
$sql = 'SELECT * FROM data WHERE data_type = "pdf" and `data_name` = ""';

$query = $db->t_query($sql, []);
$i = 0;
foreach($query as $row){
    $loc = $row['data_content'];
    $loc = explode('/', $loc);
    $filename = array_pop($loc);
    $filename = str_replace([
        '.pdf',
        '.webp'
    ], '', $filename);
    $pattern = '/\([^0-9]+\)/';
    $filename = preg_replace($pattern, '', $filename);
    $filename = trim($filename);
    $d->updateRecord([
        'name' => $filename
    ], $row['data_id']);
}*/