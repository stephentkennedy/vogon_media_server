<?php
if(empty($_GET['dir'])){
	$_GET['dir'] = '';
}
load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'session_array',
	'init_model' => 'import_init',
	'init_data' => [
		'dir' => $_GET['dir']
	],
	'model' => 'import',
	'ext' => 'media',
	'var_name' => 'f'
]);