<?php
if(!empty($_POST['action'])){
	switch($_POST['type']){
		case 'video':
			load_controller('header');
			load_controller('ajax_scan', [
				'dir' => $_POST['dir'],
				'series_name' => $_POST['series_name'],
				'series_id' => $_POST['series_id']
			], 'media');
			load_controller('footer');
			break;
		case 'audio':
			load_controller('header');
			load_controller('ajax_scan', [
				'dir' => $_POST['dir']
			], 'audio');
			load_controller('footer');
			break;
	}
	//Clean filenames and rename files as needed
	load_controller('clean_filenames', [], 'audio');
}else{
	load_controller('header');
	echo load_view('importer', [], 'media');
	load_controller('footer');
}