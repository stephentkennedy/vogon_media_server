<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	redirect(build_slug(''));
	die();
}
load_controller('header', [
	'title' => 'Find Orphan Entries'
]);
echo load_view('find_orphan_entries', [], 'server');
load_controller('footer');