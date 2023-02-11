<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	return;
}
load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'db',
	'offset' => false, //We're deleting items so we'll end up skipping if we enable the offset query.
	'sql' => 'SELECT * FROM cache WHERE 1 ORDER BY cache_id',
	'ext' => 'server',
	'model' => 'flush_cache',
	'cleanup' => 'reincrement_cache'
]);