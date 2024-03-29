<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	redirect(build_slug(''));
	die();
}
$action = get_slug_part(1);
if(!empty($action)){
	switch(strtolower($action)){
		case 'stop':
			load_model('stop_minidlna', [], 'media');
			break;
		case 'start':
			load_model('start_minidlna', [], 'media');
			break;
		case 'restart':
			load_model('stop_minidlna', [], 'media');
			load_model('start_minidlna', [], 'media');
			break;
		case 'import':
			load_controller('importer', [], 'media');
			break;
		case 'flush_cache':
			load_controller('flush_cache', [], 'server');
			break;
		case 'cleanup_data_table':
			load_controller('cleanup_data_table', [], 'server');
			break;
		case 'cleanup_data_meta_table':
			load_controller('cleanup_data_meta_table', [], 'server');
			break;
		case 'find_orphan_entries':
			load_controller('find_orphan_entries', [], 'server');
			break;
		case 'build_file_hashes':
			load_controller('build_media_hashes', [], 'server');
			break;
		case 'patch_series':
			load_controller('patch_series', [], 'server');
			break;
		case 'update':
			load_controller('update', [], 'installer');
			break;
		case 'check-for-updates':
			load_model('server_update', [], 'server');
			break;
		case 'server-restart':
			load_model('restart_server', [], 'server');
			break;
		case 'server-shutdown':
			load_model('shutdown_server', [], 'server');
			break;
		case 'ebook_series_type_change':
			load_controller('ebook_series_cleanup', [], 'ebooks');
			break;
		default:
			redirect(build_slug('server'));
			break;
	}
	
	//This switch case is because we're repurposing this controller.
	switch(strtolower($action)){
		case 'stop':
		case 'start':
		case 'restart':
		case 'check-for-updates':
		case 'server-restart':
		case 'server-shutdown':
			$string = $_SERVER['REQUEST_URI'];
			$array = explode('/', $string);
			array_pop($array);
			$string = implode('/', $array);
			header('Location: '.$string);
			break;
	}
	
}else{
	load_controller('header', [
		'title'=> 'Server Tools'
	]);
	echo load_view('minidlna', [], 'server');
	load_controller('footer');
}