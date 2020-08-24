<?php
$location = get_slug_part(1);
if(isset($_POST['action'])){
	switch(strtolower($_POST['action'])){
		case 'save-film-meta':
			if(isset($_POST['id'])){
				if(!isset($_POST['genre'])){
					$_POST['genre'] = false;
				}
				$model_data = [
					'title' => $_POST['title'],
					'location' => urldecode($_POST['file']),
					'director' => $_POST['director'],
					'release' => $_POST['release'],
					'starring' => $_POST['starring'],
					'desc' => $_POST['desc'],
					'id' => $_POST['id']
				];
				load_model('update_video', $model_data, 'media');
				$id = $_POST['id'];
			}else{
				$model_data = [
					'title' => $_POST['title'],
					'location' => urldecode($_POST['file']),
					'director' => $_POST['director'],
					'release' => $_POST['release'],
					'starring' => $_POST['starring'],
					'desc' => $_POST['desc'],
					'runtime' => $_POST['runtime'],
				];
				$id = load_model('create_video', $model_data, 'media');
			}
			break;
	}
}

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
			$genres = $db->query($sql, [])->fetchAll();
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
			Name: Stephen Kennedy
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
		load_controller('header', ['view' => 'mini']); //We control the title inside the view because it's manipulated with JavaScript
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
