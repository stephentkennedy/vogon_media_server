<?php
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
					'series' => $_POST['series'],
					'id' => $_POST['id']
				];
				if(!empty($_POST['animorphic'])){
					$model_data['animorphic'] = 1;
				}else{
					$model_data['animorphic'] = 0;
				}
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
				if(!empty($_POST['animorphic'])){
					$model_data['animorphic'] = 1;
				}else{
					$model_data['animorphic'] = 0;
				}
				$id = load_model('create_video', $model_data, 'media');
			}
			break;
		case 'delete':
			$model_data = [
				'id' => $_POST['id'],
				'delete_file' => false
			];
			if(!empty($_POST['remove_file'])){
				$model_data['delete_file'] = true;
			}
			load_model('delete_video', $model_data, 'media');
			//Since we're deleting we can't exactly go back to the page we were on.
			redirect(build_slug('', [], 'media'));
			break;
	}
}