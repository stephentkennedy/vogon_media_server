<?php
$clerk = new clerk;

if(!empty($_POST['id'])){
	$check = $clerk->getRecord($_POST['id']);
	if($check['data_type'] == 'playlist'){
		$clerk->removeRecord($_POST['id']);
	}
}