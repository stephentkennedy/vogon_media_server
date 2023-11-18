<?php
error_reporting(0);
ini_set('display_errors', 0);
global $user;
$id = get_slug_part(3);
$clerk = new clerk;
$track = $clerk->getRecord($id, true);
if(!empty($track['data_parent'])){
	$album = $clerk->getRecord($track['data_parent']);
}
$src = str_replace(ROOT, '', $track['data_content']);
$src = str_replace(DIRECTORY_SEPARATOR, '/', $src);
$src = build_slug(ltrim($src, '/'));
$mime = mime_content_type($track['data_content']);
$title = $track['data_name'];
if(empty($title)){
	$temp = explode('/', $src);
	$title = array_pop($temp);
}
if(empty($track['meta']['fav_'.$user['user_key']])){
	$track['meta']['fav_'.$user['user_key']] = 0;
}
$return = [
	'title' => $title,
	'id' => $track['data_id'],
	'link' => build_slug('edit/'.$track['data_id'], [], 'audio'),
	'src' => $src,
	'mime' => $mime,
	'duration' => $track['meta']['length'],
	'artist' => $track['meta']['artist'],
	'favorite' => $track['meta']['fav_'.$user['user_key']],
	'max_bin' => $track['meta']['max_bin'],
	'min_bin' => $track['meta']['min_bin'],
	'level_peak' => $track['meta']['level_peak'],
	'level_graph' => json_decode($track['meta']['level_graph'], true)
];
if(!empty($album)){
	$return['album'] = $album['data_name'];
	$return['album_link'] = build_slug('album/'.$album['data_id'], [], 'audio');
}else{
	$return['album'] = '';
}
if(!empty($track['meta']['history'])){
	//This means that audio and media are linked together and you can't have audio without media.
	//Honestly should look into making them one extension and supporting more routes via build_slug
	$history_data = load_controller('ajax_history', ['id' => $track['data_id']], 'media');
	$return['time'] = $history_data['watched'];
	if(empty($return['time'])){
		$return['time'] = 0;
	}
}
$return = json_encode($return);
header('Content-Type: application/json;charset=utf-8');
echo $return;
