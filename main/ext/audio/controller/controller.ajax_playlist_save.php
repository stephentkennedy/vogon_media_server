<?php
$clerk = new clerk;
foreach($_POST['list'] as $key => $value){
	$_POST['list'][$key] = (int)$value;
}
if($_POST['id'] != 'false'){
	$playlist = $clerk->getRecord($_POST['id']);
	if($playlist['data_type'] == 'playlist'){
		$update = [
			'name' => $_POST['title'],
			'content' => json_encode($_POST['list'])
		];
		$clerk->updateRecord($update, $_POST['id']);
	}
}else{
	$new = [
		'name' => $_POST['title'],
		'content' => json_encode($_POST['list']),
		'type' => 'playlist'
	];
	$clerk->addRecord($new);
}