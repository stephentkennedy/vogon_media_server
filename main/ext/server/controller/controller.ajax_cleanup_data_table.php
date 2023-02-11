<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	return;
}
load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'db',
	'sql' => 'SELECT * FROM data WHERE 1 ORDER BY `data_id`',
	'ext' => 'server',
	'model' => 'reorder_data_table',
	'cleanup' => 'reorder_data_table_cleanup'
]);