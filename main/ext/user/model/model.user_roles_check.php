<?php
load_class('db_handler');
$d = new db_handler('data');

$search = [
    'type' => 'user_role',
    'orderby' => 'create_date'
];

$user_roles = $d->getRecords($search);

return empty($user_roles);