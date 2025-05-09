<?php
$location = get_slug_part(1);

switch($location){
	case 'edit':
		if(!isset($id)){
			$id = get_slug_part(2);
		}
		if(!empty($id)){
			$clerk = new clerk;
			$record = $clerk->getRecord(['id' => $id], true);
			$title = $record['data_name'];
			$view_data = load_model('format_video_data', $record, 'media');
		}else{
			$sql = 'SELECT * FROM `data` WHERE `data_type` = "genre" ORDER BY `data_name` ASC';
			$genres = $db->t_query($sql, [])->fetchAll();
			$title = 'New Video';
			$view_data = [
				'genres' => $genres
			];
		}
		load_controller('header', ['title' => 'Edit: '.$title]);
		echo load_view('video_edit', $view_data, 'media');
		load_controller('footer');
		break;
	case 'view':
		$id = get_slug_part(2);
		if(empty($id) || !is_numeric($id)){
			header('Location: '.URI);
			die();
		}
		$clerk = new clerk;
		$record = $clerk->getRecord(['id' => $id], true);
		$view_data = load_model('format_video_data', $record, 'media');
		load_controller('header', ['title' => 'Details: '.$record['data_name']]);
		if($record['data_type'] == 'video' || $record['data_type'] == 'tv'){
			echo load_view('view_video', $view_data, 'media');
		}else if($record['data_type'] == 'series'){
			/*
			Name: Steph Kennedy
			Date: 7/29/20 
			Comment: We're having a lot of slowdown on the first load of this page. After that first load, the built in MySQL caching is speeding it up significantly, but that only lasts for around 12 hours. We need something that work similarly, but for longer.
			
			Ideally, it will also update when we make changes to series members, but we can start with just triggering that manually.
			*/
			$view_data['members'] = load_model('get_series_members', ['id' => $id], 'media');
			echo load_view('view_series', $view_data, 'media');
		}
		load_controller('footer');
		break;
	case 'season_edit':
		$id = get_slug_part(2);
		if(empty($id) || !is_numeric($id)){
			header('Location: '.URI);
			die();
		}
		$clerk = new clerk;
		$record = $clerk->getRecord(['id' => $id], true);
		$view_data = load_model('format_video_data', $record, 'media');
		$view_data['members'] = load_model('get_series_members', ['id' => $id], 'media');
		load_controller('header', ['title' => 'Season Editor: '.$record['data_name']]);
		echo load_view('season_editor', $view_data, 'media');
		load_controller('footer');
		break;
	case 'watch':
		load_controller('header', ['view' => 'nano', 'head_tags' => [
			build_style_tag('/fonts/font-awesome.min.css'),
			build_style_tag('/dist/css/video_player_module.min.css?v=5')
		]]); //We control the title inside the view because it's manipulated with JavaScript
		$id = get_slug_part(2);
		if(empty($id) || !is_numeric($id)){
			header('Location: '.URI);
			die();
		}
		$clerk = new clerk;
		$record = $clerk->getRecord(['id' => $id], true);
		$view_data = load_model('format_video_data', $record, 'media');
		echo load_view('video', $view_data, 'media');
		load_controller('footer', ['view' => 'mini']);
		break;
	default:
		load_controller('header', ['title' => 'Video Library']);
		//$video_data = load_model('get_videos', [], 'media');
	
		//echo load_view('main', $video_data, 'media');
		echo load_view('main_ajax', [], 'media');
		load_controller('footer');
		break;
}
