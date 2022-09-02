<?php
$clerk = new clerk;
$new_data = [
	'id' => $_POST['data_id'],
	'name' => $_POST['data_name'],
	'slug' => $_POST['data_slug'],
	'type' => $_POST['data_type'],
	'parent' => $_POST['data_parent'],
	'status' => $_POST['data_status']
];
if(empty($_POST['id'])){
	$clerk->addRecord($new_data);
}else{
	$clerk->updateRecord($new_data, $_POST['id']);
}