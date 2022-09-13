<?php
load_class('db_handler');
global $user;
$h = new db_handler('data');
$record = [
    'name' => $title,
    'content' => $url,
    'user_key' => $user['user_key'],
    'type' => 'rss_feed'
];
if(isset($id)){
    //Update Branch
    $h->updateRecord($record, $id);
}else{
    //Create Branch
    $id = $h->addRecord($record);
}

$search = [
    'type' => 'rss_feed_cache',
    'user_key' => $user['user_key'],
];

//Clear our feed cache when sources are changed.
$feed_cache = $h->getRecord($search);
if(!empty($feed_cache)){
    $cache_id = $feed_cache['data_id'];
    $update = [
        'content' => '',
        'last_edit' => db_date('now')
    ];
    $h->updateRecord($update, $cache_id);
}

return $id;