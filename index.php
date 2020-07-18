<?php
if(!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'new_install')){
	include __DIR__.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'bootstrap.php';
}else{
	define('NEW', true);
	include __DIR__ . DIRECTORY_SEPARATOR . 'installer.php';
}
?>