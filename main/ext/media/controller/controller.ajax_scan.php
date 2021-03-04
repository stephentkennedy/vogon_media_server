<?php
$route = 'ajax/ajax_import/media?dir=' . urlencode($dir);
if(!empty($series_name)){
	$route .= '&series_name=' . urlencode($series_name);
}
if(!empty($series_id)){
	$route .= '&series_id=' . urlencode($series_id);
}
echo load_view('ajax_scan', [
	'route' => $route
], 'media');