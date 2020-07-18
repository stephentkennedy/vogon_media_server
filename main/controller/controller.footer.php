<?php
	$footer_data = load_model('footer');
	if(isset($view) && $view == 'mini'){
		echo load_view('minifooter', $footer_data);
	}else{
		echo load_view('footer', $footer_data);
	}
?>