<?php
$uptime = shell_exec('uptime');
$uptime = preg_split('/,\s+/', trim($uptime));

$temp = preg_split('/\s+/', $uptime[0]);
$timestamp = $temp[0];
$days = $temp[2];
$temp = explode(':', $timestamp);
$hours = $temp[0];
$minutes = $temp[1];
$seconds = $temp[2];
$temp = preg_split('/\s+/', $uptime[2]);
$users = $temp[0];
$one = str_replace('load average: ', '', $uptime[3]);
$five = $uptime[4];
$fifteen = $uptime[5];

//Memory Stuff
$free = shell_exec('free -m');
$free = explode(PHP_EOL, $free);
foreach($free as $line => $content){
	$free[$line] = preg_split('/\s+/', $content);
}
$memory = [
	'total' => $free[1][1],
	'used' => $free[1][2],
	'free' => $free[1][3],
	'shared' => $free[1][4],
	'buff/cache' => $free[1][5],
	'available' => $free[1][6]
];

$swap = [
	'total' => $free[2][1],
	'used' => $free[2][2],
	'free' => $free[2][3]
];

echo load_view('top', [
	'memory' => $memory, 
	'swap' => $swap,
	'days' => $days,
	'hours' => $hours,
	'minutes' => $minutes,
	'seconds' => $seconds,
	'users' => $users,
	'one' => $one,
	'five' => $five,
	'fifteen' => $fifteen
], 'server');