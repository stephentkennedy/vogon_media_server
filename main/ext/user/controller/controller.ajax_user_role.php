<?php
global $user_model;
load_class('db_handler');
$d = new db_handler('data');
$search = [
    'type' => 'user_role',
    'id' => $_GET['role']
];
$user_role = $d->getRecord($search);
if(!empty($user_role)){
    $role = $user_role['data_content'];
    $role = json_decode($role, true);
    if(
        !$user_model->permission('admin_permissions')
        && $role['admin_permissions']
    ){
        return null;
    }
    echo load_view('json', $role);
}