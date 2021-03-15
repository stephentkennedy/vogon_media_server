<?php
//We're going to have to do some error handling here and only do subsequent loops if we haven't encountered an error already.
if(!isset($_SESSION['error'])){
	$_SESSION['error'] = false;
}
switch($step){
	case 'download':
		if($_SESSION['error'] == false){
			return load_model('download_update', [], 'installer');
		}
		break;
	case 'extract':
		if($_SESSION['error'] == false){
			return load_model('extract_update', [], 'installer');
		}
		break;
	case 'database':
		if($_SESSION['error'] == false){
			return load_model('db_update', [], 'installer');
		}
		break;
	case 'move_files':
		if($_SESSION['error'] == false){
			return load_model('file_update', [], 'installer');
		}
		break;
	default: 
		return $step;
		break;
}