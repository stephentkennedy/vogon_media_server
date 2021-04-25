<?php

//Set our base directory and update our installed version.
$base = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update';
$folders = array_values(array_diff(scandir($base), ['.','..']));
$folder = $base . DIRECTORY_SEPARATOR . $folders[0] . DIRECTORY_SEPARATOR;
$new_ver = file_get_contents($folder . 'ver');
load_model('increment_version', ['new_version' => $new_ver], 'installer');

$files = recursiveScan($base, true);

$files = array_reverse($files);
foreach($files as $f){
	unlink($f);
}
$zip = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update.zip';
unlink($zip);
return 'Removed Update Files.';