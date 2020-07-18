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
			$files_to_add[ROOT . DIRECTORY_SEPARATOR . $file] = $file;
		}
	}
	
	$zip = new ZipArchive();
	$filename = ROOT . DIRECTORY_SEPARATOR . $filename;
	if(file_exists($filename)){
		unlink($filename);
	}
	if($zip->open($filename, ZipArchive::CREATE) !== TRUE){
		die('cannot open ' . $filename);
	}
	
	$files_to_add = array_reverse($files_to_add, true);
	debug_d($files_to_add);
	foreach($files_to_add as $real => $relative){
		$zip->addFile($real, $relative);
	}
	$zip->addFromString('new_install', 'You have not installed your copy of vogon yet');
	$zip->deleteName('main' . DIRECTORY_SEPARATOR . 'config.ini');
	
	foreach($files as $f){
		$zip->addFile(ROOT . DIRECTORY_SEPARATOR . $f, $f);
	}
	
	return $zip->close();