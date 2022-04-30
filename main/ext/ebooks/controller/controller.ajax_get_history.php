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
    echo load_view('json', [
        'history' => false
    ]);
}else{
    echo load_view('json', [
        'history' => $check['history_val']
    ]);
}