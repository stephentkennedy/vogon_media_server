<?php
global $user_model;
if(!$user_model->permission('sys_info')){
	return;
}
load_class('vParse');
$v = new vParse;
$level = $_GET['level'];
if(!empty($_GET['version'])){
	$version = $_GET['version'];
}else{
	$version = false;
}
echo $v->increment($level, $version);