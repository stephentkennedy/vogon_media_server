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
$memory['percent'] = (int)$memory['used'] / (int)$memory['total'];
$memory['percent'] = round($memory['percent'] * 100);
$memory['total'] = round_up_mbs($memory['total']);
$memory['used'] = round_up_mbs($memory['used']);
$memory['available'] = round_up_mbs($memory['available']);

$swap['percent'] = (int)$swap['used'] / (int)$swap['total'];
$swap['percent'] = round($swap['percent'] * 100);
$swap['total'] = round_up_mbs($swap['total']);
$swap['used'] = round_up_mbs($swap['used']);
$swap['free'] = round_up_mbs($swap['free']);
?><h4>Time Online</h4><?php
	echo $days;
	if($days > 1){
		echo ' days, ';
	}else{
		echo ' day, ';
	}
	echo $hours;
	if($hours > 1){
		echo ' hours, ';
	}else{
		echo ' hour, ';
	}
	echo $minutes;
	if($minutes > 1){
		echo ' minutes';
	}else{
		echo ' minute';
	}
?><h4>Active Users</h4>
<?php echo $users; ?>
<h4>Load Averages</h4>
<?php echo 'One Minute: '.$one.'<br>Five Minutes: '.$five.'<br>Fifteen Minutes: '.$fifteen; ?>
<h4>Memory Usage</h4>
Available: <?php echo $memory['total']; ?>  Used: <?php echo $memory['used'].' ('.$memory['percent'].'%)'; ?>
<h4>Swap File Usage</h4>
Available: <?php echo $swap['total']; ?> Used: <?php echo $swap['used'].' ('.$swap['percent'].'%)'; ?>