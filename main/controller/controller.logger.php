<?php
global $user;
if(empty($message)){
	$message = $_REQUEST['message'];
	if(empty($message)){
		//If we don't have anything to log, then we have no business running.
		return;
	}
}

if(empty($log)){
	$log = $_REQUEST['log'];
	if(empty($log)){
		$log = 'misc';
	}
}

if(empty($user['user_key'])){
	if(!empty($key)){
		$_REQUEST['key'] = $key;
	}
	if(empty($_REQUEST['key'])){
		return;
	}
	if(!empty($source)){
		$_REQUEST['source'] = $source;
	}
	//We need to verify that this is something that has credentials to be logged. Something that should really only happen when something is sending us information remotely.
	load_model('verify_log_key', ['key' => $_REQUEST['key'], 'message' => $message, 'log' => $log]);
	
	$source = $_REQUEST['source'];
}else{
	$source = $user['user_key'];
}
//If we want we can overwrite the source with a string if that will be more helpful.
if(!empty($_REQUEST['source']) && !is_numeric($_REQUEST['source'])){
	$source = $_REQUEST['source'];
	if(!empty($user['user_key'])){
		//Still gonna attribute it to a user though.
		$source .= ' ('.$user['user_key'].')';
	}
}

load_model('log', ['message' => $message, 'log' => $log, 'source' => $source]);