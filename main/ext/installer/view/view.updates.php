<header><h1>Updates</h1></header>
<h2>Your Version: <?php echo VER; ?></h2>
<h2>Available: <?php echo $most_recent; ?></h2>
<?php if($greater == true){
	echo 'There is an update available.';
	echo '<fieldset><legend>Change Log</legend>';
	echo nl2br($change_log);
	echo '</fieldset>';
}else{
	echo 'You are currently on the most recent version, but you can re-install the update if you think there is an issue in your install.';
} ?><br><br>
<a class="button" href="<?php echo build_slug('update/install', [], 'server'); ?>">Update</a>