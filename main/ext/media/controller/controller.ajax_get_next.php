<?php


$return = load_model('get_next_member', ['id' => (int)$_GET['id']], 'media');

header('Content-Type: application/json;charset=utf-8');
echo json_encode($return);