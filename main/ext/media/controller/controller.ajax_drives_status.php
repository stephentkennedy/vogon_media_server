<?php
$df = shell_exec('df -h');

$lines = explode(PHP_EOL, $df);
$headings = preg_split('/\s+/', $lines[0]);
$parsed = [];
for($i = 1; $i < count($lines); $i++){
	$l = $lines[$i];
	$temp = preg_split('/\s+/', $l);
	$save = [];
	foreach($temp as $key => $value){
		$save[$headings[$key]] = $value;
	}
	if(trim($save['Filesystem']) == '/dev/root'
	|| stristr($save['Filesystem'], '/dev/sd') !== false){
		$parsed[] = $save;
	}
}
echo load_view('drives', ['drives' => $parsed], 'server');