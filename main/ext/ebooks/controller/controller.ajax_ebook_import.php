<?php
if(empty($_GET['dir'])){
	$_GET['dir'] = '';
}
load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'session_array',
	'init_model' => 'ebook_import_init',
	'init_data' => [
		'dir' => $_GET['dir']
	],
	'model' => 'ebook_import',
	'ext' => 'ebooks',
	'var_name' => 'f'
]);
