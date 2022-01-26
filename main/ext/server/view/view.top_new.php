<div class="row">
	<div class="col col-three">
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
		?>
	</div>
	<div class="col col-three">
		<h4>Active Shell Users</h4>
		<?php echo $users; ?>
	</div>
	<div class="col col-three">
		<h4>Active Web Users</h4>
		<?php echo $web_users; ?>
	</div>
</div>
<h4>CPU Load Averages</h4>
<h5>One Minute</h5>
<?php echo load_view('percentage_bar', [
	'percent' => $one_per,
	'label' => $one . ' ('.$one_per.'%)'
], 'server'); ?>
<h5>Five Minutes</h5>
<?php echo load_view('percentage_bar', [
	'percent' => $five_per,
	'label' => $five . ' ('.$five_per.'%)'
], 'server'); ?>
<h5>Fifteen Minutes</h5>
<?php echo load_view('percentage_bar', [
	'percent' => $fifteen_per,
	'label' => $fifteen . ' ('.$fifteen_per.'%)'
], 'server'); ?>
<h4>RAM</h4>
<?php echo load_view('percentage_bar', [
	'label' => $memory['friendly_used'].' of '.$memory['friendly_total'].' ('.$memory['percent'].'%)',
	'percent' => $memory['percent']
], 'server'); ?>
<h4>Swap File Usage</h4>
<?php echo load_view('percentage_bar', [
	'label' => $swap['friendly_used'].' of '.$swap['friendly_total'].' ('.$swap['percent'].'%)',
	'percent' => $swap['percent']
], 'server'); ?>
<style>
.percentage-bar .inner-bar.high{
	background: red;
}
.percentage-bar .inner-bar.medium{
	background: orange;
}
</style>
