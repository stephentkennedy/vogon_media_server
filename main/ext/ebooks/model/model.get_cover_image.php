<?php
$item = load_model('get_item_by_id', ['id' => $item_id], 'ebooks');

$page = 0;

$page_data = load_model('get_cbz_image_data', [
    'item' => $item,
    'page' => $page
], 'ebooks');

if(!empty($page_data['image_data'])){
    return $page_data['image_data'];
}else{
    return load_view('json', array_keys($page_data));
}