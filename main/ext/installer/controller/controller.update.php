<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	redirect(build_slug(''));
	die();
}
$action = get_slug_part(2);
switch($action){
	case 'install':
		load_controller('header', ['title' => 'Installing Update']);
		echo load_view('ajax_installer', [], 'installer');
		load_controller('footer');
		break;
	default:
		$update_info = load_model('check_for_updates', [], 'installer');
		load_controller('header', ['title' => 'Updates']);
		echo load_view('updates', $update_info, 'installer');
		load_controller('footer');
		break;
}