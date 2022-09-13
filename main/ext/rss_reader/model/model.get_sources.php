<?php
load_class('db_handler');
global $user;
$h = new db_handler('data');
$search = [
    'type' => 'rss_feed',
    'user_key' => $user['user_key'],
    'orderby' => 'name'
];
$feeds = $h->getRecords($search);

if(empty($feeds)){
    return [];
}
return $feeds;