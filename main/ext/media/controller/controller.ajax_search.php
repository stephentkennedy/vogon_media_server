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
	$rpp = 24;
}
if(!empty($_GET['format'])){
	$format = $_GET['format'];
}else{
	$format = 'JSON';
}
$model_data = load_model('search', ['search' => $search, 'type' => $type, 'page' => $page, 'rpp' => $rpp], 'media');
if($model_data['error'] == false){
	
	$page_data = load_model('page', [
		'page' => $page,
		'ipp' => $rpp,
		'count' => $model_data['count']
	], 'audio'); //This is used by both video and audio now, maybe move? Reclassify as a core since pageination is something we'll need to do for literally any data
	$model_data['page_data'] = $page_data;
	$model_data['format'] = $format;

	echo load_view('ajax_items', $model_data, 'media');
}else{
	debug_d('Search Failed:');
	debug_d($model_data['sql']);
	debug_d($model_data['params']);
}