<?php
function round_up_mbs($value){
	$unit = 'MB';
	if($value > 1024){
		$value = $value / 1024;
		$unit = 'GB';
		if($value > 1024){
			$value = $value / 1024;
			$unit = 'TB';
		}
	}
	return round($value, 2) . $unit;
}
load_class('db_handler');
$s = new db_handler('session');
$check = db_date('-5 minutes');
$search = [
	'greater_than_last_edit' => $check
];
$active_sessions = count($s->getRecords($search));


$uptime = shell_exec('uptime');
$uptime = preg_split('/,\s+/', trim($uptime));
$cpus = (int)trim(shell_exec('nproc --all'));

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
	'total' => (int)$free[1][1],
	'friendly_total' => round_up_mbs((int)$free[1][1]),
	'used' => (int)$free[1][2],
	'friendly_used' => round_up_mbs((int)$free[1][2]),
	'free' => (int)$free[1][3],
	'shared' => $free[1][4],
	'buff/cache' => $free[1][5],
	'available' => $free[1][6],
	'friendly_available' => round_up_mbs($free[1][6]),
];
$memory['percent'] = $memory['used'] / $memory['total'];
$memory['percent'] = round($memory['percent'] * 100);

$swap = [
	'total' => (int)$free[2][1],
	'friendly_total' => round_up_mbs($free[2][1]),
	'used' => (int)$free[2][2],
	'friendly_used' => round_up_mbs($free[2][2]),
	'free' => (int)$free[2][3],
	'friendly_free' => round_up_mbs($free[2][3]),
];
$swap['percent'] = $swap['used'] / $swap['total'];
$swap['percent'] = round($swap['percent'] * 100);

echo load_view('top_new', [
	'memory' => $memory, 
	'swap' => $swap,
	'days' => $days,
	'hours' => $hours,
	'minutes' => $minutes,
	'seconds' => $seconds,
	'users' => $users,
	'web_users' => $active_sessions,
	'one' => $one,
	'one_per' => (round($one / $cpus, 4) * 100),
	'five' => $five,
	'five_per' => (round($five / $cpus, 4) * 100),
	'fifteen' => $fifteen,
	'fifteen_per' => (round($fifteen / $cpus, 4) * 100),
	'cpus' => $cpus,
], 'server');
