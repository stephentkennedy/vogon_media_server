<?php
/*
Name: Steph Kennedy
Date: 12/4/2018
Comment: Let's establish what we need for our bootstrap;
*/
error_reporting(E_ERROR | E_WARNING | E_PARSE);
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
if(!empty($_SERVER['HTTP_HOST'])){
	$host = $_SERVER['HTTP_HOST'];
}else{
	$host = '';
}
$protocol=$_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
define('URL', $protocol.'://'.$host);

//Create our Database Connection
$db = new thumb(__DIR__.DIRECTORY_SEPARATOR.'config.ini');

$sql = 'SELECT * FROM var WHERE var_session = 1';
$query = $db->t_query($sql, []);
session_start();
//Dump session variables not set by the database
//$_SESSION = [];
$session_vars = $query->fetchAll();
foreach($session_vars as $var){
	$_SESSION[$var['var_name']] = $var['var_content'];
}

if(isset($_SESSION['error_reporting'])){
	//error_reporting($_SESSION['error_reporting']);
}

//Include our framework functions
include __DIR__.DIRECTORY_SEPARATOR.'functions.php';

if(phpversion() < 8){
	//This allows us to use legacy extensions if we're on a low enough php version to allow the use of $thumb->query without errors.
	load_class('db_shim');
	$db2 = new db_shim(__DIR__.DIRECTORY_SEPARATOR.'config.ini');
	$db = $db2;
}

//Load our installed extensions as a session variable
$exts = dir_contents(__DIR__ . DIRECTORY_SEPARATOR . 'ext');
$_SESSION['loaded_extensions'] = $exts;

if(defined('SKIP_ROUTER') && SKIP_ROUTER == true){
	run_filters('cli_init');
	return;
}

//Comment this out if you don't want vogon to be password protected
global $user,
	$user_model;
load_class('user_model');
$user_model = new user_model([
	'secure' => false,
	'session_timeout' => 'twelve hours ago'
]);
$user_model->manage_session();
$user = [
	'user_key' => $user_model->user_key
];
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

//Used by the Filebrowser Ext, comment out if you're not using that ext
$_SESSION['active_filebrowsers'] = 0;

run_filters('vogon_init');

/*
Developer: Steph Kennedy
Date: 4/23/21
Comment: While working in WordPress I've discovered how many problems are caused by not having a clean way for people to register filters on an output buffer so we're just gonna add that into the mix
*/

ob_start('outputFilters');

//Include our router
include __DIR__.DIRECTORY_SEPARATOR.'router.php';

ob_flush();
ob_end_clean();

//Install Admin Role if not exist;
if(load_model('user_roles_check', [], 'user')){
	redirect(build_slug('', ['tab' => 'user'], 'settings'));
}
