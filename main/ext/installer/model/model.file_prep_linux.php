<?php
	$directories = [
		'main',
		'js',
		'css',
	];
	if(file_exists(ROOT . DIRECTORY_SEPARATOR . 'fonts')){
		$directories[] = 'fonts';
	}
	if(file_exists(ROOT . DIRECTORY_SEPARATOR . 'vendor')){
		$directories[] = 'vendor';
	}
	$files = [
		'index.php',
		'.htaccess',
		'installer.php'
	];
	$files_to_add = [];
	foreach($directories as $dir){
		$f = recursiveScan($dir, true);
		foreach($f as $file){
			$files_to_add[ROOT . DIRECTORY_SEPARATOR . $file] = str_replace(DIRECTORY_SEPARATOR, '/', $file);
		}
	}
	return $files_to_add;