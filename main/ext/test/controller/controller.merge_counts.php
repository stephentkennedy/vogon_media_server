<?php
ini_set('display_errors', 1);
load_class('db_handler');
set_time_limit(0);
$d = new db_handler('data');
$dm = new db_handler('data_meta');
$dwm = $d->meta_link($dm, [
    'meta_key_field' => 'data_meta_name',
    'meta_value_field' => 'data_meta_content'
]);


$search = [
    'type' => 'cbz',
    'meta' => [
        'order'
    ],
    'limit' => '10',
    'orderby' => 'data_id',
    'orderby_dir' => 'desc'
];

$sql = 'SELECT `data`.*, m1.`data_meta_id`, m1.`data_meta_content` AS `order`, m1.`count` AS `count` FROM `data` LEFT JOIN (SELECT *, count(data_meta_id) as `count` FROM `data_meta` WHERE `data_meta_name` = "order" GROUP BY `data_id` ORDER BY `data_meta_id` ASC) m1 on `data`.`data_id` = m1.`data_id` WHERE `data`.`data_type` = :type AND `count` > 0 ORDER BY `count` DESC';

$params = [
    ':type' => 'cbz'
];

$results = $db->t_query($sql, $params)->fetchAll();

//$results = $dwm->getRecords($search);

//debug_d($dwm->sql);


//debug_d(count($results));
//die();



foreach($results as $result){

    if($result['count'] <= 1){
        continue;
    }

    $sql = 'DELETE FROM `data_meta` WHERE `data_meta_name` = "order" AND `data_id` = :id AND `data_meta_id` > :meta_id';

    $params = [
        ':id' => $result['data_id'],
        ':meta_id' => $result['data_meta_id']
    ];

    $db->t_query($sql, $params);
}