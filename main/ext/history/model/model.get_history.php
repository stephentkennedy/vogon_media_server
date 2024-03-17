<?php

global $user;
load_class('db_handler');
ini_set('display_errors', 1);
$history = new db_handler('history');
$data = new db_handler('data');
$hwd = $history->direct_link($data, [
    'join_key' => 'data_id',
    'child_join_key' => 'data_id'
]);

$search = [
    'user_key' => $user['user_key'],
    'orderby' => 'last_edit',
    'orderby_dir' => 'desc',
    'limit' => $rpp,
    'offset' => (($page - 1) * $rpp)
];

$results = $hwd->getRecords($search);

$return = [
    'search_results' => $results,
    'count' => $hwd->total_count,
    'error' => false
];

return $return;