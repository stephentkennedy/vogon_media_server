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