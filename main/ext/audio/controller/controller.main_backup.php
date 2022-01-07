<?php

$action = get_slug_part(1);
$clerk = new clerk;
switch($action){
	case 'listen':
		$id = get_slug_part(2);
		if(!is_numeric($id)){
			header('Location: /');
			die();
		}
		$audio = $clerk->getRecord(['id' => $id]);
		$mime_type = mime_content_type($audio['data_content']);
		$track = str_replace([ROOT, ' '], ['', '%20'], $audio['data_content']);
		$view_data = [
			'track' => $track,
			'mime_type' => $mime_type
		];
		load_controller('header', ['view' => 'mini']);
		echo load_view('audio', $view_data, 'audio');
		load_controller('footer', ['view' => 'mini']);
		
		break;
	case 'page':
		
		$page = get_slug_part(2);
		
		if(!is_numeric($page)){
			header('Location: /');
			die();
		}
		
		$start = $page * 25;
		
		$search_data = [
			'type' => 'audio',
			'orderby' => 'data_name',
			'limit' => $start.',25'
		];

		$audio_tracks = $clerk->getRecords($search_data, true);
		$count = $clerk->total_count;

		$search_data = [
			'type' => 'album',
			'orderby' => 'data_id'
		];
		/*$albums_raw = $clerk->getRecords($search_data);
		$albums = [];
		foreach($albums_raw as $a){
			$albums[$a['data_id']] = $a['data_name'];
		}*/
		$albums = load_model('get_albums', ['tracks' => $audio_tracks], 'audio');
		
		$page_data = load_model('page', [
			'page' => $page,
			'ipp' => 25,
			'count' => $count
		], 'audio');

		$view_data = [
			'audio_tracks' => $audio_tracks,
			'albums' => $albums,
			'page_data' => $page_data
		];

		load_controller('header');
		echo load_view('main', $view_data, 'audio');
		load_controller('footer');
	
		break;
	default:

		$search_data = [
			'type' => 'audio',
			'orderby' => 'data_name',
			'limit' => '0,25'
		];

		$audio_tracks = $clerk->getRecords($search_data, true);
		$count = $clerk->total_count;

		$albums = load_model('get_albums', ['tracks' => $audio_tracks], 'audio');

		$page_data = load_model('page', [
			'page' => 1,
			'ipp' => 25,
			'count' => $count
		], 'audio');

		$view_data = [
			'audio_tracks' => $audio_tracks,
			'albums' => $albums,
			'page_data' => $page_data
		];

		load_controller('header');
		echo load_view('main', $view_data, 'audio');
		load_controller('footer');
		break;
}