<?php
load_class('db_handler');
$d = new db_handler('data');
$dm = new db_handler('data_meta');
$dwm = $d->meta_link($dm, [
    'meta_key_field' => 'data_meta_name',
    'meta_value_field' => 'data_meta_content'
]);

$search = [
    'id' => $_GET['id'],
    'meta' => [
		'sub_series',
		'order'
	],
];

$current_issue = $dwm->getRecord($search);
if(empty($current_issue)){
    $returned = [
        'error' => true,
        'message' => 'No issue at that id'
    ];
    echo load_view('json', $returned);
    return;
}
$search_2 = [
    'parent' => $current_issue['data_parent'],
    'meta' => [
		'sub_series',
		'order'
	],
    'meta_sub_series' => $current_issue['sub_series'],
    'meta_order' => ((int)$current_issue['order'] + 1),
    'orderby' => 'meta_order'
];
$next_issue = $dwm->getRecord($search_2);
if(!empty($next_issue)){
    echo load_view('json', $next_issue);
}else{
    $returned = [
        'error' => true,
        'message' => 'Could not find next issue.',
        'sql' => $dwm->sql,
        'params' => $dwm->params
    ];
    echo load_view('json', $returned);
}