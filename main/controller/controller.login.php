<?php 
$users = load_model('get_users', [], 'user');
load_controller('header', [
    'view' => 'mini',
    'title' => 'login'
]);
echo load_view('login', ['users' => $user], 'users');
load_controller('footer', ['view' => 'mini']);
die(); //No additional routing from the login page.