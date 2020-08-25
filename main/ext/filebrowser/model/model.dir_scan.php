<?php
//Get the next directory up
if($dir != DIRECTORY_SEPARATOR){
	$dir = rtrim($dir, DIRECTORY_SEPARATOR);
}
$up = explode(DIRECTORY_SEPARATOR, $dir);
array_pop($up);
$up = implode(DIRECTORY_SEPARATOR, $up);

$contents = dir_contents($dir);
$dirs = [];
$files = [];
foreach($contents as $f){
	$loc = $dir . DIRECTORY_SEPARATOR . $f;
	if(is_dir($loc)){
		$dirs[] = [
			'name' => $f,
			'loc' => $loc
		];
	}else{
		$mime = mime_content_type($loc);
		$files[] = [
			'name' => $f,
			'loc' => $loc,
			'mime' => $mime
		];
	}
}

return [
	'dir_up' => $up,
	'dirs' => $dirs,
	'files' => $files
];