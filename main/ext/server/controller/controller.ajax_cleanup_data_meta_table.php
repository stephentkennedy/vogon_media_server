<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	return;
}
load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'db',
	'sql' => 'SELECT * FROM data_meta WHERE 1 ORDER BY `data_meta_id`',
	'ext' => 'server',
	'model' => 'reorder_data_meta_table',
	'cleanup' => 'reorder_data_meta_table_cleanup'
]);