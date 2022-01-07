<?php
global $user;
$key = 'fav_'.$user['user_key'];

load_class('db_handler');
$dm = new db_handler('data_meta');
$search = [
	'name' => $key,
	'data_id' => $id
];

$check = $dm->getRecord($search);

if(empty($check)){
	$search['content'] = 1;
	$dm->addRecord($search);
}else{
	if($check['data_meta_content'] == 1){
		$search['content'] = 0;
	}else{
		$search['content'] = 1;
	}
	$dm->updateRecord($search, $check['data_meta_id']);
}
