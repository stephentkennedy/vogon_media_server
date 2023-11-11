<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	redirect(build_slug(''));
	die();
}
load_controller('header', [
	'title' => 'Build File Hashes'
]);
echo load_view('build_file_hashes', [], 'server');
load_controller('footer');