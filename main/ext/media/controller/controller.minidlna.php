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
	}
	$string = $_SERVER['REQUEST_URI'];
	$array = explode('/', $string);
	array_pop($array);
	$string = implode('/', $array);
	header('Location: '.$string);
}else{
	load_controller('header');
	echo load_view('minidlna', [], 'media');
	load_controller('footer');
}