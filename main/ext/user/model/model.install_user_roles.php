<?php
load_class('db_handler');

//Data Table, simple object
$d = new db_handler('data');

//User Table
$u = new db_handler('user');

//Add our user role record
$record = [
    'name' => 'admin',
    'content' => json_encode([
        'role_name' => 'Admin',
        'admin_permissions' => true,
        'settings' => true,
        'edit' => true,
        'password' => true,
        'sys_info' => true,
        'history' => false,
        'multi_session' => false
    ]),
    'type' => 'user_role'
];
$admin_role_id = $d->addRecord($record);

$user_search = [
    'not_key' => 0,
    'orderby' => 'user_key',
    'limit' => 1
];

$new_admin = $u->getRecord($user_search);

if(empty($new_admin)){
    debug_d($u->db->error);
    die();
}

$admin_id = $new_admin['user_key'];

$u->updateRecord([
    'role' => $admin_role_id,
    'role_mods' => json_encode([
        'password' => false,
        'password_nag' => true
    ])
],$admin_id);

return $d->getRecord($admin_role_id);