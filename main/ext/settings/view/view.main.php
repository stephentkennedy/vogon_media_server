<?php
	load_controller('header', ['title' => 'Settings']);
	echo $settings;
	if(!empty($_GET['force_reload']) && $_GET['force_reload'] == true){
		echo '<script type="text/javascript">window.location = "'.URI.'/settings";</script>';
	}
	load_controller('footer');
?>