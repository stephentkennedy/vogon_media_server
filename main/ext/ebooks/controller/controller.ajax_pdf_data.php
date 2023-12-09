<?php
ini_set('memory_limit', '4G');
$id = $_REQUEST['data_id'];
$item = load_model('get_item_by_id', ['id' => $id], 'ebooks');

$pdf_data = load_model('get_pdf_data', ['item' => $item], 'ebooks');

echo load_view('json', $pdf_data);