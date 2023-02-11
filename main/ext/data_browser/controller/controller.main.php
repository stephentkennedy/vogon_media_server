<?php
/*
Developer: Steph Kennedy
Date: 4/28/21
Comment: There are often times when I've wanted to make manual updates to the data table, because an import ran wrong or I needed to reassign data. This tool is designed to be a way to do that without requiring the user to learn any SQL or PHPMyAdmin.
*/
global $user_model;
if(!$user_model->permission('sys_info')){
	redirect(build_slug(''));
	die();
}
if(empty($_GET['offset'])){
	$offset = 0;
}else{
	$offset = (int)$_GET['offset'];
}
if(empty($_GET['limit'])){
	$limit = 25;
}else{
	$limit = (int)$_GET['limit'];
}
if(empty($_GET['search'])){
	$search = false;
}else{
	$search = $_GET['search'];
}
if(empty($_GET['search_col'])){
	$search_col = 'all';
}else{
	$search_col = $_GET['search_col'];
}
if(empty($_GET['order_by'])){
	$order_by = 'data_id';
}else{
	$order_by = $_GET['order_by'];
}
if(empty($_GET['dir'])){
	$dir = 'ASC';
}else{
	$dir = $_GET['dir'];
}

$model_data = [
	'offset' => $offset,
	'limit' => $limit,
	'search' => $search,
	'search_col' => $search_col,
	'order_by' => $order_by,
	'dir' => $dir
];

$results = load_model('get_rows', $model_data, 'data_browser');
load_controller('header', ['title' => 'Data Browser']);
echo load_view('data_view', $results, 'data_browser');
load_controller('footer');