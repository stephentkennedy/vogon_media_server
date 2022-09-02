<?php
	//$var = explode('?', $_SERVER['REQUEST_URI'])[0];
	//$var = explode('/', $var);
	$controller = get_slug_part(1);
	if(!empty(get_slug_part(2))){
		$ext = get_slug_part(2);
	}
	if(empty($ext)){
		load_controller($controller, ['method' => 'ajax']);
	}else{
		load_controller($controller, ['method' => 'ajax'], $ext);
	}
?>