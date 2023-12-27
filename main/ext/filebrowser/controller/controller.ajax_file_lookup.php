<?php
load_class('db_handler');
$data = new db_handler('data');

$search = [
    'search_content' => urldecode($_POST['search'])
];

$result = $data->getRecord($search);

$to_return = load_model('get_links', ['record' => $result, 'search' => $search['search_content']], 'filebrowser');

echo load_view('json', $to_return);