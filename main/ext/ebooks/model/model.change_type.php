<?php 
load_class('db_handler');
$d = new db_handler('data');
$search = [
    'type' => ['pdf', 'cbz'],
    'self_join' => [
        'index_field' => 'data_parent',
        'fields' => [
            'data_name',
            'data_type'
        ],
    ],
    'groupby' => 'data_parent'
];
$update = [
    'type' => $new_type
];

$check = $d->getRecords($search);

if(empty($check)){
    debug_d($d->sql);
    debug_d($d->params);
    debug_d($d->db->error);
    die();
}
foreach($check as $series){
    if($series['parent_data_type'] == $old_type){
        $check2 = $d->updateRecord($update, $series['data_parent']);
        if($check2 == false){
            debug_d($d->sql);
            debug_d($d->params);
            debug_d($d->db->error);
            die();
        }
    }
}

redirect(build_slug('', [], 'server'));