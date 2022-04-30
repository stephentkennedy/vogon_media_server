<?php 
/*
    Supported ebook types
*/
$supported = [
    //'cbr',
    'cbz',
    'pdf',
];
$id = get_slug_part(2);
if(!is_numeric($id)){
    return  [
        'error'=> true,
        'message' => 'Data id is not valid.'
    ];
}

$item = load_model('get_item_by_id', ['id' => $id], 'ebooks');
$type = $item['data_type'];

if(!in_array($type, $supported)){
    return  [
        'error'=> true,
        'message' => 'Data type is not supported by this controller.'
    ];
}

$view_data = [
    'item' => $item,
    'type' => $type
];
switch($type){
    case 'pdf':
        $view = 'js_pdf_viewer';
        break;
    case 'cbz':
    case 'cbr':
        $view = 'comic_book_reader';
        break;
}
load_controller('header', ['title' => $item['data_name'], 'view' => 'mini']);
echo load_view($view, ['item'=> $item], 'ebooks');
load_controller('footer', ['view'=>'mini']);