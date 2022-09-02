<?php
if(empty($_SESSION['active_filebrowsers'])){
	$_SESSION['active_filebrowsers'] = 1;
}else{
	$_SESSION['active_filebrowsers']++;
}
if(!empty($b_file)){
	$b_file = trueLoc($b_file);
	$dir = dirname($b_file);
}else{
	$b_file = false;
}
if(empty($dir) || !file_exists($dir)){
	$dir = ROOT;
}
$dir_data = load_model('dir_scan', ['dir' => $dir], 'filebrowser');
$preloaded = load_view('dir', $dir_data, 'filebrowser');
if(empty($form)){
	$form = false;
}
switch($form){
	case 'dir':
		$form = 'dir';
		break;
	case 'file':
		$form = 'file';
		break;
	default:
		$form = false;
		break;
}
echo load_view('ajax_filebrowse', ['preload' => $preloaded, 'form' => $form, 'dir' => $dir, 'b_file' => $b_file], 'filebrowser');