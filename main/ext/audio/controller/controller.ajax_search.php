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
$model_data = load_model('search', ['search' => $search, 'type' => $type, 'page' => $page, 'rpp' => $rpp], 'audio');
if($model_data['error'] == false){
	
	$page_data = load_model('page', [
		'page' => $page,
		'ipp' => $rpp,
		'count' => $model_data['count']
	], 'audio');
	$model_data['page_data'] = $page_data;
	$model_data['format'] = $format;

	echo load_view('ajax_items', $model_data, 'audio');
}else{
	debug_d('Search Failed:');
	debug_d($model_data['sql']);
	debug_d($model_data['params']);
}