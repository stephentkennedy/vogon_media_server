<?php
load_class('db_handler');
global $user;
$h = new db_handler('history');
$search = [
    'user_key' => $user['user_key'],
    'data_id' => $_GET['id'],
];

$check = $h->getRecord($search);
if(empty($check)){
    $search['val'] = $_GET['page'];
    $search['last_edit'] = db_date('now');
    $h->addRecord($search);
}else{
    debug_d($check);
    $h->updateRecord([
        'val' => $_GET['page'],
        'last_edit' => db_date('now')
    ], $check['history_id']);
}