<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	redirect(build_slug(''));
	die();
}
load_controller('header', [
	'title' => 'Data table cleanup.'
]);
echo load_view('reorder_data_table', [], 'server');
load_controller('footer');