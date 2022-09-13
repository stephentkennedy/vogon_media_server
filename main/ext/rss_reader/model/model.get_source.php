<?php
load_class('db_handler');
global $user;
$h = new db_handler('data');
$search = [
    'id' => $id,
    'type' => 'rss_feed',
    'user_key' => $user['user_key']
];
$feeds = $h->getRecord($search);

if(empty($feeds)){
    return [];
}
return $feeds;