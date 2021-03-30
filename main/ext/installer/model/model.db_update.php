<?php
/*
Name: Stephen Kennedy
Date: 3/15/21
Comment: First things first, we want to be able to install updates that are generated in multiple ways so we need to find our root directory of the update we're looking to install.

The easiest way for us to do that is to look for whatever folder has /main/ in it, and make sure that /main/bootstrap.php, /main/functions.php, and /main/router.php exist.

The model here recursively does that until it finds the folder, or returns false if it could not find it.
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);
$update_root = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update';

$update_root = load_model('find_update_root', ['start_dir' => $update_root], 'installer');

if($update_root != false){
	$db_root = $update_root . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR;
	
	$db_struct = $db_root . 'struct.json';
	$db_tables = $db_root . 'tables.json';
	
	$settings = parse_ini_file(ROOT . DIRECTORY_SEPARATOR . 'main'. DIRECTORY_SEPARATOR . 'config.ini', true);
	$db_name = $settings['database']['name'];
	$sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = :name';
	$params = [
		':name' => $db_name
	];
	
	$table_return = $db->query($sql, $params)->fetchAll();
	$tables = [];
	foreach($table_return as $t){
		$tables[$t['table_name']] = false;
	}
	
	$model_data = [
		'cur_struct' => load_model('get_struct', ['tables' => $tables], 'installer'),
		'new_struct' => json_decode(file_get_contents($db_struct), true)
	];
	
	//debug_d($model_data);
	
	$db_diff = load_model('db_diff', $model_data, 'installer');
	
	load_model('install_new_tables', ['new' => $db_diff['new'], 'db_tables' => json_decode(file_get_contents($db_tables), true)], 'installer');
	
	load_model('update_tables', ['change' => $db_diff['change']], 'installer');
}