<?php
	if(empty($title)){$title = '';}
	$header_data = load_model('header', ['title' => $title]);
	if(empty($header_data['logo'])){
		$header_data['logo'] = URI.'/upload/favicon.png';
	}
	if(empty($header_data['logo_text'])){
		$header_data['logo_text'] = NAME;
	}
	if(empty($view)){
		$view = 'standard';
	}
	switch($view){
		case 'mini':
			echo load_view('miniheader', $header_data);
			break;
		case 'nano':
			echo load_view('nanoheader', $header_data);
			break;
		default:
			echo load_view('header', $header_data);
			break;
	}
?>