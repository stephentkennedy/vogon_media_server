<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
load_class('ajax_loop_interface');

$ali = new ajax_loop_interface([
	'mode' => 'db',
	'sql' => 'SELECT * FROM `data` WHERE `data_type` = "video" ORDER BY `data_name`',
	'ext' => 'test',
	'model' => 'ali_db_mode_parse',
	'var_name' => 'row_test'
]);