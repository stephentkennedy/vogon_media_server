<?php
if($_SESSION['minidlna_dir'] != '/' && $_SESSION['minidlna'] == 'minidlnad'){
	shell_exec('xargs kill <'.$_SESSION['minidlna_dir']. DIRECTORY_SEPARATOR .'minidlna.pid');
}else{
	shell_exec('sudo systemctl stop minidlna');
}