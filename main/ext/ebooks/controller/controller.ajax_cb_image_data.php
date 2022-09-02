<?php 
$id = $_GET['id'];
$page = $_GET['page'];
$item = load_model('get_item_by_id', ['id' => $id], 'ebooks');
switch($item['data_type']){
    case 'cbz':
        $image_data = load_model('get_cbz_image_data', ['item' => $item, 'page' => $page], 'ebooks');
        break;
    case 'cbr':
        $image_data = load_model('get_cbr_image_data', ['item' => $item, 'page' => $page], 'ebooks');
        break;
}
echo load_view('json', $image_data);