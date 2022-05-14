<?php

	if(empty($_REQUEST) && empty($_GET['form'])){

	}else if(empty($_REQUEST['action']) || $_REQUEST['action'] != 'search'){
		if(empty($_GET['ext'])){
			load_controller('settings', ['mode' => 'save']);
		}else{
			load_controller('settings', ['mode' => 'save'], $_GET['ext']);
		}
	}
	
	$settings = [];
	$settings[] = [
		'name' => 'framework',
		'form' => load_controller('settings', ['mode' => 'get_form'])
	];
	$exts = $_SESSION['loaded_extensions'];
	foreach($exts as $ext){
		if($ext != '.' && $ext != '..'){
			$settings[] = [
			'name' => $ext,
			'form' => str_replace('{{ext_name}}', $ext, load_controller('settings', ['mode' => 'get_form'], $ext))];
		}
	}
	load_controller('header', ['title' => 'Settings']);
	echo load_view('main', [
		'settings' => $settings
	], 'settings');
	if(!empty($_GET['force_reload']) && $_GET['force_reload'] == true){
		echo '<script type="text/javascript">window.location = "'.URI.'/settings";</script>';
	}
	load_controller('footer');
?>