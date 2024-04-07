<?php
set_time_limit(0);
load_class('db_handler');
$d = new db_handler('data');
$dm = new db_handler('data_meta');
$dwm = $d->meta_link($dm, [
    'meta_key_field' => 'data_meta_name',
    'meta_value_field' => 'data_meta_content'
]);
$search = [
    'type' => 'cbz',
    'meta' => [
        'sub_series'
    ]
];
$pattern = '/\s0+/';

$sql = 'SELECT * FROM `data_meta` WHERE `data_meta_name` = "sub_series" GROUP BY `data_meta_content`';

$results = $db->t_query($sql, [])->fetchAll();

foreach($results as $row){
    $search['meta_sub_series'] = $row['data_meta_content'];
    $members = $dwm->getRecords($search);
    $comic_key = [];
    foreach($members as $m){
        $m['data_name'] = preg_replace($pattern, ' ', strtolower($m['data_name']));
        $comic_key[$m['data_id']] = $m['data_name'];
    }
    natsort($comic_key);
    $i = 0;
    foreach($comic_key as $data_id => $name){
        $check_search = [
            'data_id' => $data_id,
            'name' => 'order'
        ];
        $check = $dm->getRecord($check_search);
        if($check){
            $update = [
                'content' => $i++
            ];
            $dm->updateRecord($update, $check['data_meta_id']);
        }else{
            $add = [
                'data_id' => $data_id,
                'name' => 'order',
                'content' => $i++
            ];
            $dm->addRecord($add);
        }
    }
}