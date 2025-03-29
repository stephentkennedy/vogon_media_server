<?php
load_class('db_handler');
global $user;
$h = new db_handler('data');
$fish = new fish;
//RSS feeds should be small, text only, and we're not loading external resources as part of the curl request.
$timeout = get_var('rss_timeout');
if(
    empty($timeout)
    || !is_numeric($timeout)
){
    $timeout = 5;
}
$fish->options[CURLOPT_TIMEOUT] = $timeout;

$feeds = load_model('get_sources', [], 'rss_reader');

if(empty($feeds)){
    return [];
}

$feed_data = [];
$feed_errors = [];
foreach($feeds as $feed){
    $fish->url = $feed['data_content'];
    $fish->dispatch();
    $feed_content = $fish->raw;
	
    $info = $fish->info;
    if(!empty($feed_content)){
        $feed_xml = simplexml_load_string($feed_content);
        if(!empty($feed_xml)){
            $rss = $feed_xml->rss;
            if(empty($rss)){
                $rss = $feed_xml;
            }
			if(isset($rss->channel)){
				//RSS Spec
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
						if(empty($channel_item['link'])){
							$channel_item['link'] = $item->link['href'];
						}
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
			}else{
				//Atom Spec
				foreach($rss->entry as $item){
					$channel_title = $rss->title->__toString();
					$channel_link = $rss->link['href']->__toString();
					$channel_items = [];
					$channel_item = [
						'title' => $item->title->__toString(),
						'desc' => $item->desc->__toString(),
						'date' => $item->updated->__toString(),
						'link' => $item->link['href']->__toString(),
						'image' => false
					];
					$channel_items[] = $channel_item;
					$feed_data[] = [
						'title' => $channel_title,
						'link' => $channel_link,
						'desc' => $channel_desc,
						'items' => $channel_items
					];
				}
			}
        }
    }else{
        if(
            is_string($info)
            && stristr($info, 'Connection timed out') !== false
        ){
            $feed_errors[] = [
                'message' => 'Connection to '.$feed['data_name'].' timed out.',
                'type' => 'timeout'
            ];
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
if(!empty($feed_errors)){
    $feed_data['errors'] = $feed_errors;
}
return $feed_data;
