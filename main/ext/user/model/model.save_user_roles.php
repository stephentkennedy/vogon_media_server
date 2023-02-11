<?php
global $user_model;
load_class('db_handler');
$d = new db_handler('data');


$ap = false;
if(!empty($_POST['admin_permissions'])){
    $ap = true;
}
$s = false;
if(!empty($_POST['settings'])){
    $s = true;
}
$e = false;
if(!empty($_POST['edit'])){
    $e = true;
}
$p = false;
if(!empty($_POST['req_password'])){
    $p = true;
}
$si = false;
if(!empty($_POST['sys_info'])){
    $si = true;
}
$h = false;
if(!empty($_POST['history'])){
    $h = true;
}
$ms = false;
if(!empty($_POST['multi_session'])){
    $ms = true;
}
$new_permissions = [
    'role_name' => $_POST['role_name'],
    'admin_permissions' => $ap,
    'settings' => $s,
    'edit' => $e,
    'password' => $p,
    'sys_info' => $si,
    'history' => $h,
    'multi_session' => $ms
];
$new_permissions = json_encode($new_permissions);


if(!$user_model->permission('settings')){
    //Users who cannot set settings
    return;
}

$role_id = $_POST['role_id'];
if(is_numeric($role_id) && $role_id != 0){
    $old_role = $d->getRecord($role_id);
    if(empty($old_role)){
        //Won't update what doesn't exist.
        return;
    }

    $old_permissions = json_decode($old_role['data_content'], true);
    if(
        !$user_model->permission('admin_permissions')
        && $old_permissions['admin_permissions']
    ){
        //Users who aren't admins cannot edit admin roles.
        return;
    }

    $d->updateRecord([
        'content' => $new_permissions
    ], $old_role['data_id']);
}else{
    $name = slugify($_POST['role_name']);
    $new_record = [
        'type' => 'user_role',
        'content' => $new_permissions,
        'name' => $name
    ];
    $d->addRecord($new_record);
}