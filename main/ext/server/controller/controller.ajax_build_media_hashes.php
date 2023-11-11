<?php
global $user_model;
if(!$user_model->permission('sys_info')){
    return;
}

$media_types = load_model('get_media_types', [], 'server');

load_class('db_handler');
$d = new db_handler('data');
$dm = new db_handler('data_meta');
$d_dm = $d->meta_link($dm, [
    'meta_key_field' => 'data_meta_name',
    'meta_value_field' => 'data_meta_content'
]);

$search = [
    'meta' => [
        'file_hash'
    ],
    'type' => $media_types
];

$query = $d_dm->get_sql($search);

//debug_d($sql); die();

load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
    'mode' => 'db',
    'sql' => $query['sql'],
    'params' => $query['params'],
    'ext' => 'server',
    'model' => 'build_file_hashes'
]);