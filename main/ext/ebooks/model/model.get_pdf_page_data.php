<?php

$model_data = [
    'source' => $item['data_content'],
    'page' => $page
];

$pdf_string = load_model('get_single_page_pdf', $model_data, 'ebooks');

$pdf_string = chunk_split(
    base64_encode(
        $pdf_string['pdf']
    )
);

return [
    'pdf' => $pdf_string
];