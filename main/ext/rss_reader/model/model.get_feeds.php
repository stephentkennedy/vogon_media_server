<?php
load_class('db_handler');
global $user;
$h = new db_handler('data');
$fish = new fish;

$feeds = load_model('get_sources', [], 'rss_reader');

if(empty($feeds)){
    return [];
}

$feed_data = [];
foreach($feeds as $feed){
    $fish->url = $feed['data_content'];
    $fish->dispatch();
    $feed_content = $fish->raw;
    $info = $fish->info;
    if(!empty($feed_content)){
        $feed_xml = simplexml_load_string($feed_content);
        //debug_d(json_encode($feed_xml, JSON_PRETTY_PRINT));
        if(!empty($feed_xml)){
            $rss = $feed_xml->rss;
            if(empty($rss)){
                $rss = $feed_xml;
            }
            foreach($rss->channel as $channel){
                $channel_title = $channel->title->__toString();
                $channel_link = $channel->link->__toString();
                $channel_desc = $channel->description->__toString();
                $channel_items = [];
                foreach($channel->item as $item){
                    $channel_item = [
                        'title' => $item->title->__toString(),
                        'desc' => $item->desc->__toString(),
                        'date' => $item->pubDate->__toString(),
                        'link' => $item->link->__toString(),
                        'image' => false
                    ];
                    if(!empty($item->image)){
                        $channel_item['image'] = [
                            'src' => $item->image->url->__toString(),
                            'title' => $item->image->title->__toString(),
                            'desc' => $item->image->description->__toString()
                        ];
                    }else if(!empty($channel->image)){
                        $channel_item['image'] = [
                            'src' => $channel->image->url->__toString(),
                            'title' => $channel->image->title->__toString(),
                            'desc' => $channel->image->description->__toString()
                        ];
                    }
                    $channel_items[] = $channel_item;
                }
                $feed_data[] = [
                    'title' => $channel_title,
                    'link' => $channel_link,
                    'desc' => $channel_desc,
                    'items' => $channel_items
                ];
            }
        }
    }
}

$search = [
    'type' => 'rss_feed_cache',
    'user_key' => $user['user_key'],
    //'last_edit' => db_date()
];
$feed_cache = $h->getRecord($search);
if(!empty($feed_cache)){
    $cache_id = $feed_cache['data_id'];
    $cached_data = json_encode($feed_data);
    $update = [
        'content' => $cached_data,
        'last_edit' => db_date('now')
    ];
    $h->updateRecord($update, $cache_id);
}

return $feed_data;