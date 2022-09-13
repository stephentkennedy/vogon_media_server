<?php
$action = get_slug_part(1);
$feeds_check = load_model('get_sources', [], 'rss_reader');
if(empty($feeds_check)){
    $action = 'sources';
}
if(empty($action)){
    load_controller('header', ['title' => 'RSS Feed']);
    $feed = load_model('cache_check', [], 'rss_reader');
    echo load_view('feed', ['feed' => $feed], 'rss_reader');
    load_controller('footer');
}else{
    switch($action){
        case 'sources':
            load_controller('sources', ['sources' => $feeds_check], 'rss_reader');
            break;
    }
}