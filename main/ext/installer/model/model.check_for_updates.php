<?php

$cache_file = ext_root('server') . DIRECTORY_SEPARATOR . 'check.ver';
$change_file = ext_root('server') . DIRECTORY_SEPARATOR . 'change_log';
$get = false;

//This should keep us from pestering github more than once a day. For all other requests we'll reference our own cache file.
if(file_exists($cache_file)){
	$check = filemtime($cache_file);
	$time = time();
	$distance = 60 * 60 * 24;
	if(($time - $check) > $distance){
		$get = true;
	}
}else{
	$get = true;
}

if($get == true){
	$fish = new fish;
	//We'll need to create some kind of interface for establishing this so we can use this same code on other versions of vogon.
	$fish->url = 'https://raw.githubusercontent.com/stephentkennedy/vogon_media_server/master/ver';
	$fish->dispatch();
	$ver = $fish->raw;
	file_put_contents($cache_file, $ver);
	
	$fish->url = 'https://raw.githubusercontent.com/stephentkennedy/vogon_media_server/master/change_log';
	$fish->dispatch();
	$change_log = $fish->raw;
	file_put_contents($change_file, $change_log);	
}else{
	$ver = file_get_contents($cache_file);
	if(file_exists($change_file)){
		$change_log = file_get_contents($change_file);
	}else{
		$change_log = 'No change log exists for this update.';
	}
}

load_class('vParse');
$v = new vParse;
return [
	'most_recent' => $ver,
	'greater' => $v->greater($ver),
	'change_log' => $change_log
];