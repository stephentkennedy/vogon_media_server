<?php

$rpp = 25;
$page = 1;

if(isset($_GET['page'])){
    $page = $_GET['page'];
}
if(isset($_GET['rpp'])){
    $rpp = $_GET['rpp'];
}

$model_data = [
    'page' => $page,
    'rpp' => $rpp
];

$results = load_model('get_history', $model_data, 'history');

$page_data = load_model('page', [
    'page' => $page,
    'ipp' => $rpp,
    'count' => $results['count']
], 'audio');
$results['page_data'] = $page_data;

echo load_view('ajax_items', $results, 'history');