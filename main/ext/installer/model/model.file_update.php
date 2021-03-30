<?php

$ignore = [
	'~^example_config/.*$~',
	'~^install_scripts/.*$~',
	'~^new_install$~',
	'~^ver$~',
	'~^.htaccess$~'
];

$base = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update';

$folders = array_values(array_diff(scandir($base), ['.','..']));

$folder = $base . DIRECTORY_SEPARATOR . $folders[0] . DIRECTORY_SEPARATOR;
//debug_d($folder);

$files = recursiveScan(rtrim($folder, '/'), true);
$count = 0;
foreach($files as $u_file){
	$simplified_name = ltrim(str_replace($folder, '', $u_file), '/');
	//debug_d($simplified_name);
	$skip = false;
	foreach($ignore as $pattern){
		$check = preg_match($pattern, $simplified_name);
		if($check == 1){
			$skip = true;
		}
	}
	if($skip == false){
		$new_name = ROOT . DIRECTORY_SEPARATOR . $simplified_name;
		
		//Make our directories if they don't exist.
		$folders = explode('/', $simplified_name);
		$depth = '';
		
		//Drop our filename;
		array_pop($folders);
		
		foreach($folders as $f){
			$test = ROOT . DIRECTORY_SEPARATOR . $depth . $f;
			if(!file_exists($test)){
				mkdir($test);
			}
			$depth .= $f . DIRECTORY_SEPARATOR;
		}
		
		//Move our file
		rename($u_file, $new_name);
		chmod($new_name, 0775);
		//debug_d($u_file);
		//debug_d($new_name);
		$count++;
	}
}
return $count . ' files installed.';