<?php
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
			load_model('flush_cache', [], 'media');
			break;
	}
	
	//This switch case is because we're repurposing this controller.
	switch(strtolower($action)){
		case 'stop':
		case 'start':
		case 'restart':
		case 'flush_cache':
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
	echo load_view('minidlna', [], 'media');
	load_controller('footer');
}