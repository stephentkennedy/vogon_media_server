<?php
	$zip = new ZipArchive();
	$filename = ROOT . DIRECTORY_SEPARATOR . $filename;
	if(file_exists($filename)){
		unlink($filename);
	}
	if($zip->open($filename, ZipArchive::CREATE) !== TRUE){
		die('cannot open ' . $filename);
	}
	
	$files_to_add = array_reverse($files_to_add, true);
	foreach($files_to_add as $real => $relative){
		echo 'Adding: '.$real.'<br>';
		$zip->addFile($real, $relative);
	}
	$zip->addFromString('new_install', 'You have not installed your copy of vogon yet');
	$zip->addFromString('ver', $version);
	$zip->deleteName('main' . '/' . 'config.ini');
	$files = [
		'index.php',
		'cli.php',
		'.htaccess',
		'installer.php',
		'change_log'
	];
	foreach($files as $f){
		$zip->addFile(ROOT . DIRECTORY_SEPARATOR . $f, $f);
	}
	
	return $zip->close();