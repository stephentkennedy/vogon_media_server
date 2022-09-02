<?php 
$users = load_model('get_users', [], 'user');
load_controller('header', [
    'view' => 'mini',
    'title' => 'login'
]);
echo load_view('nonsecure_login', ['users' => $users], 'user');
load_controller('footer', ['view' => 'mini']);
die(); //No additional routing from the login page.