<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	redirect(build_slug(''));
	die();
}
load_controller('header', [
	'title' => 'Flush Cache'
]);
echo load_view('flush_cache', [], 'server');
load_controller('footer');