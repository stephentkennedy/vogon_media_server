<?php
if(!empty($_GET['search'])){
	$search = $_GET['search'];
}else{
	$search = false;
}
$type = false;
$page = 1;
$rpp = 24;
$format = 'JSON';

$model_data = load_model('search_all', ['search' => $search, 'type' => $type, 'page' => $page, 'rpp' => $rpp], 'media');
if($model_data['error'] == false){
	$model_data['format'] = $format;

	echo load_view('ajax_items', $model_data, 'media');
}else{
	debug_d('Search Failed:');
	debug_d($model_data['sql']);
	debug_d($model_data['params']);
}