<?php
$base = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update';

$files = recursiveScan($base, true);

$files = array_reverse($files);
foreach($files as $f){
	unlink($f);
}
$zip = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update.zip';
unlink($zip);
return 'Removed Update Files.';