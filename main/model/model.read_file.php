<?php
	//If we aren't writing the file to our own directory, and it isn't relative, don't do it.
	if(stristr($filename, ROOT) === false && stristr($filename, DIRECTORY_SEPARATOR) !== false){
		return false;
	}
	if(stristr($filename, DIRECTORY_SEPARATOR) === false){
		if(isset($ext)){
			$filename = 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . $filename;
		}
			$filename = ROOT . DIRECTORY_SEPARATOR . $filename;
	}
	debug_d($filename);
	return [
		//'filename' => $filename,
		'content' => file_get_contents($filename)
	];