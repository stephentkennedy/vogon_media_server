<?php
load_class('db_handler');
$d = new db_handler('data');

$search = [
    'type' => 'user_role',
    'orderby' => 'create_date'
];

$user_roles = $d->getRecords($search);

if(empty($user_roles)){
    $admin_role = load_model('install_user_roles', [], 'user');
    $user_roles = [
        $admin_role
    ];
}

global $user_model;
if(!$user_model->permission('admin_permissions')){
    foreach($user_roles as $key => $role){
        $role_data = json_decode($role['data_content'], true);
        if($role_data['admin_permissions'] == true){
            unset($user_roles[$key]);
        }
    }
}

return $user_roles;