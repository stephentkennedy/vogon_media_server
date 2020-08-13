<?php
	if(isset($_GET['dir'])){
		$dir = $_GET['dir'];
	}
	if(empty($dir)){
		$dir = ROOT;
	}
	if(stristr($dir, ROOT) == -1){
		$dir = ROOT . DIRECTORY_SEPARATOR . $dir;
	}
	$dir_data = load_model('dir_scan', ['dir' => $dir], 'filebrowser');
	load_controller('header');
	echo load_view('dir', $dir_data, 'filebrowser');
	echo load_view('browser', [], 'filebrowser');
	load_controller('footer');