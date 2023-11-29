<?php
if(!empty($_GET['search'])){
	$search = $_GET['search'];
}else{
	$search = false;
}
if(!empty($_GET['type'])){
	$type = $_GET['type'];
}else{
	$type = false;
}
if(!empty($_GET['page'])){
	$page = $_GET['page'];
}else{
	$page = 1;
}
if(!empty($_GET['rpp'])){
	$rpp = $_GET['rpp'];
}else{
	$rpp = 25;
}
if(!empty($_GET['format'])){
	$format = $_GET['format'];
}else{
	$format = 'JSON';
}
if(!empty($_GET['series'])){
	$series = $_GET['series'];
}else{
	$series = '';
}
if(!empty($_GET['sub_series'])){
	$sub_series = $_GET['sub_series'];
}else{
	$sub_series = '';
}
if(!empty($_GET['not_series'])){
	$not_series = $_GET['not_series'];
}else{
	$not_series = '';
}
if(!empty($_GET['not_sub_series'])){
	$not_sub_series = $_GET['not_sub_series'];
}else{
	$not_sub_series = '';
}
$model_data = load_model('parsed_search', [
	'search' => $search, 
	'type' => $type,
	'series' => $series,
	'not_series' => $not_series,
	'sub_series' => $sub_series,
	'not_sub_series' => $not_sub_series,
	'page' => $page, 
	'rpp' => $rpp
], 'ebooks');
if($model_data['error'] == false){
	
	$page_data = load_model('page', [
		'page' => $page,
		'ipp' => $rpp,
		'count' => $model_data['count']
	], 'audio');
	$model_data['page_data'] = $page_data;
	$model_data['format'] = $format;

	echo load_view('ajax_items', $model_data, 'ebooks');
}else{
	debug_d('Search Failed:');
	debug_d($model_data['sql']);
	debug_d($model_data['params']);
}