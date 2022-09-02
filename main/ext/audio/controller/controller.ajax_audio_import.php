<?php
if(empty($_GET['dir'])){
	$_GET['dir'] = '';
}
load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'session_array',
	'init_model' => 'audio_import_init',
	'init_data' => [
		'dir' => $_GET['dir']
	],
	'model' => 'audio_import',
	'ext' => 'audio',
	'var_name' => 'f'
]);