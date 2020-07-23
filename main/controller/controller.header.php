<?php
	if(empty($title)){$title = '';}
	$header_data = load_model('header', ['title' => $title]);
	if(isset($view) && $view == 'mini'){
		echo load_view('miniheader', $header_data);
	}else{
		echo load_view('header', $header_data);
	}
?>