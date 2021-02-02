<?php
/*
Name: Stephen Kennedy
Date: 1/10/21
Comment: This is the controller that enables shell commands through the server interface
*/
$action = get_slug_part(1);
$confirm = get_slug_part(2);
if(!empty($action)){
	switch($action){
		case 'shutdown':
			load_model('shutdown', [], 'server');
			break;
		case 'restart':
			load_model('restart', [], 'server');
		default:
			break;
	}
}else{
	load_controller('header');
	echo load_view('main', [], 'server');
	load_controller('footer');
}