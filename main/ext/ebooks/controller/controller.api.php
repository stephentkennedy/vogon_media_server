<?php

$route = get_slug_part('1');

switch($route){
    case 'test':
        echo load_model('get_cover_image', ['item_id' => 34820], 'ebooks');
        break;
    case 'identify_missing':
        $page = $_GET['page'] ?? null;
        $missing = load_model('identify_missing', [], 'ebooks');
        echo load_view('json', $missing);
        break;
    case 'get_cover':
        $item_id = $_GET['id'];
        echo load_model('get_cover_image', ['item_id' => $item_id], 'ebooks');
        break;
    default:
        return false;
}