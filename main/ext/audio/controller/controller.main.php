<?php
if(isset($_REQUEST['action'])){
	switch($_REQUEST['action']){
		case 'edit': //Should only appear as a POST
			load_model('edit_item', $_POST, 'audio');
			break;
		case 'ajax_edit': //Should only appear as a POST
			load_model('edit_item', $_POST, 'audio');
			die('saved');
			break;
		case 'enable_history': //Should only appear as a GET
			$id = get_slug_part(2);
			load_model('enable_history', ['id' => $id], 'audio');
			redirect(build_slug('album/'.$id, [], 'audio'));
			break;
		case 'disable_history': //SHould only appear as a GET
			$id = get_slug_part(2);
			load_model('enable_history', ['id' => $id], 'audio');
			redirect(build_slug('album/'.$id, [], 'audio'));
			break;
	}
}
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
		
		//Fairly certain this is no longer getting hit, but it needs to be updated to support the Ajax functionality of the main view.
		//Don't want to pull a Tumblr here.
		
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
	case 'edit':
		
		//This is going to be a dedicated screen for now, but for easy editing, it would probably make more sense to have it be a ajax form via the apopup api.
		
		$id = get_slug_part(2);
		$audio = $clerk->getRecord(['id' => $id], true);
		if(!empty($audio['data_parent'])){
			$album = $clerk->getRecord(['id' => $audio['data_parent']]);
			$audio['album'] = $album['data_name'];
		}else{
			$audio['album'] = '';
		}
		load_controller('header', ['title' => 'Edit: '.$audio['data_name']]);
		echo load_view('edit', $audio, 'audio');
		load_controller('footer');
	
		break;
	case 'album':
		$id = get_slug_part(2);
		$view_data = load_model('get_album', ['album' => $id], 'audio');
		load_controller('header', ['title' => $view_data['album']['data_name']]);
		echo load_view('album', $view_data, 'audio');
		load_controller('footer');
		break;
	case 'artist':
		$id = get_slug_part(2);
		$view_data = load_model('get_artist', ['artist' => $id], 'audio');
		load_controller('header', ['title' => $view_data['artist']]);
		echo load_view('artist', $view_data, 'audio');
		load_controller('footer');
	default:
		$type = get_slug_part(1);
		
		load_controller('header', ['title' => 'Audio Library']);
		echo load_view('main_ajax', ['type' => $type], 'audio');
		load_controller('footer');
		break;
}