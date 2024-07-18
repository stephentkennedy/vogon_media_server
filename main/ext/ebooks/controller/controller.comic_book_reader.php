<?php 
/*
    Supported ebook types
*/
$supported = [
    //'cbr',
    'cbz',
    'pdf',
    'epub'
];
$id = get_slug_part(2);

if(isset($_GET['test'])){
    $item = false;
    $type = 'epub';
}else{
    if(!is_numeric($id)){
        return  [
            'error'=> true,
            'message' => 'Data id is not valid.'
        ];
    }
    $item = load_model('get_item_by_id', ['id' => $id], 'ebooks');
    $type = $item['data_type'];
}

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
$view_size = 'mini';
switch($type){
    case 'pdf':
        $view = 'pdf_viewer.v2';
        //$view = 'pdf_viewer';
        $view_size = 'mini';
        $item['url'] = load_model('file_to_url', ['item' => $item], 'ebooks');
        break;
    case 'cbz':
    case 'cbr':
        $view = 'comic_book_reader';
        break;
    case 'epub':
        //Short circuit this controller so some more complex logic can be run
        load_controller('epub_reader', [
            'item' => $item
        ], 'ebooks');
        die();
        break;
}
load_controller('header', ['title' => $item['data_name'], 'view' => $view_size]);
echo load_view($view, ['item'=> $item], 'ebooks');
load_controller('footer', ['view'=>'mini']);