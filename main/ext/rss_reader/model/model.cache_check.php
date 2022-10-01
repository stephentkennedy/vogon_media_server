<?php
load_class('db_handler');
global $user;
$h = new db_handler('data');
$search = [
    'type' => 'rss_feed_cache',
    'user_key' => $user['user_key']
];
$feed = $h->getRecord($search);
if(!empty($feed) && !empty($feed['data_content'])){
    if(
        strtotime($feed['last_edit']) > strtotime('-1 hour')
        //&& false
    ){
        $feed = json_decode($feed['data_content'], true);
        return $feed;
    }
}else if(empty($feed)){
    //Go ahead and create our cache item so that we can use it once the feed is generated.
    $h->addRecord($search);
}
return load_model('get_feeds', [], 'rss_reader');