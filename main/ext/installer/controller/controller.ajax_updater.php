<?php

load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'session_array',
	'init_model' => 'update_init',
	'model' => 'update_loop',
	'cleanup' => 'update_cleanup',
	'ext' => 'installer',
	'var_name' => 'step'
]);