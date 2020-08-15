<?php
/*
Name: Stephen Kennedy
Date: 12/4/2018
Comment: Let's establish what we need for our bootstrap;
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);
$directories = explode(DIRECTORY_SEPARATOR,__DIR__);
array_pop($directories);
$directories = implode(DIRECTORY_SEPARATOR,$directories);
define('ROOT', $directories);
date_default_timezone_set('America/New_York');

//Autoload classes from the auto folder.
$auto_classes = scandir(__DIR__.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'auto');
foreach($auto_classes as $class){
	if($class != '.' && $class != '..'){
		$test = str_replace(['class.', '.php'], '', $class);
		if(!class_exists($test)){
			include_once __DIR__.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'auto'.DIRECTORY_SEPARATOR.$class;
		}
	}
}

//Define the constants from our settings
$settings = parse_ini_file(__DIR__.DIRECTORY_SEPARATOR.'config.ini', true);
$settings = $settings['app_constants'];
foreach($settings as $const => $val){
	define(strtoupper($const), $val);
}
unset($settings);

//A little more constants
$host = $_SERVER['HTTP_HOST'];
$protocol=$_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
define('URL', $protocol.'://'.$host);

//Create our Database Connection
$db = new thumb(__DIR__.DIRECTORY_SEPARATOR.'config.ini');

$sql = 'SELECT * FROM var WHERE var_session = 1';
$query = $db->query($sql, []);
session_start();
$session_vars = $query->fetchAll();
foreach($session_vars as $var){
	$_SESSION[$var['var_name']] = $var['var_content'];
}

//Include our framework functions
include __DIR__.DIRECTORY_SEPARATOR.'functions.php';

//Load our installed extensions as a session variable
$exts = dir_contents(__DIR__ . DIRECTORY_SEPARATOR . 'ext');
$_SESSION['loaded_extensions'] = $exts;

//Comment this out if you don't want vogon to be password protected
$user = [];
//load_controller('session', [], 'user');

//Comment this out if you don't want local_storage based user management
load_controller('nonsecure_session', [], 'user');

//Default logic for search system
if(empty($_REQUEST['search'])){
	$_REQUEST['search'] = '';
}
if(empty($_REQUEST['orderby'])){
	$_REQUEST['orderby'] = '';
}

//Include our router
include __DIR__.DIRECTORY_SEPARATOR.'router.php';