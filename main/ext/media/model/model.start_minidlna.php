<?php
if($_SESSION['minidlna_dir'] != '/' && $_SESSION['minidlna'] == 'minidlnad'){
	shell_exec($_SESSION['minidlna'].' -f '.$_SESSION['minidlna_dir']. DIRECTORY_SEPARATOR . 'minidlna.conf -P '.$_SESSION['minidlna_dir']. DIRECTORY_SEPARATOR . 'minidlna.pid');
}else{
	shell_exec('sudo systemctl start minidlna');
}