<?php
	$header_data = load_model('header');
	if(isset($view) && $view == 'mini'){
		echo load_view('miniheader', $header_data);
	}else{
		echo load_view('header', $header_data);
	}
?>