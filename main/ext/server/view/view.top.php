<h4>Time Online</h4><?php
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
?><h4>Active Shell Users</h4>
<?php echo $users; ?>
<h4>Active Web Users</h4>
<?php echo $web_users; ?>
<h4>Load Averages</h4>
<?php echo 'One Minute: '.$one_per.'%<br>Five Minutes: '.$five_per.'%<br>Fifteen Minutes: '.$fifteen_per.'%'; ?>
<h4>Memory Usage</h4>
Available: <?php echo $memory['friendly_total']; ?><br>Used: <?php echo $memory['friendly_used'].' ('.$memory['percent'].'%)'; ?>
<h4>Swap File Usage</h4>
Available: <?php echo $swap['friendly_total']; ?><br>Used: <?php echo $swap['friendly_used'].' ('.$swap['percent'].'%)'; ?>
