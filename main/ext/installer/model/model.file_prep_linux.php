<?php
	$directories = [
		'main',
		'js',
		'dist',
		'scss',
		'css',
		'upload'
	];
	if(file_exists(ROOT . DIRECTORY_SEPARATOR . 'fonts')){
		$directories[] = 'fonts';
	}
	/*if(file_exists(ROOT . DIRECTORY_SEPARATOR . 'vendor')){
		$directories[] = 'vendor';
	}*/
	$files = [
		'index.php',
		'.htaccess',
		'installer.php',
		'change_log',
		'webpack.config.js',
		'package.json',
	];
	if(file_exists(ROOT . DIRECTORY_SEPARATOR . 'composer.json')){
		$files[] = 'composer.json';
	}
	$files_to_add = [];
	foreach($directories as $dir){
		$f = recursiveScan($dir, true);
		foreach($f as $file){
			if($dir != 'upload' || $f == 'favicon.png'){
				$files_to_add[ROOT . DIRECTORY_SEPARATOR . $file] = str_replace(DIRECTORY_SEPARATOR, '/', $file);
			}
		}
	}
	return $files_to_add;