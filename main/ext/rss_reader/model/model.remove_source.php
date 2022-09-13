<?php
global $user;
load_class('db_handler');
$h = new db_handler('data');
$search = [
    'user_key' => $user['user_key'],
    'type' => 'rss_feed',
    'id' => $id
];
$check = $h->getRecord($search);
if($check){
    $h->removeRecord($id);
}