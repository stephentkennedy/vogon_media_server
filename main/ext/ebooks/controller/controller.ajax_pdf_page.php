<?php
//Hopefully, shouldn't need the memory limit quite as high for this.
$id = $_REQUEST['data_id'];
$item = load_model('get_item_by_id', ['id' => $id], 'ebooks');

$page = $_REQUEST['page'];

$pdf_data = load_model('get_pdf_page_data', [
    'item' => $item,
    'page' => $page
], 'ebooks');

$meta_data = load_model('get_pdf_info', [
    'filepath' => $item['data_content']
], 'ebooks');
$page_data = load_model('parse_pdf_pages', $meta_data, 'ebooks');

//Insert the number of pages
$pdf_data = array_merge($pdf_data, $page_data);

echo load_view('json', $pdf_data);