<?php

$file_loc = $item['data_content'];

$pdf = chunk_split(
    base64_encode(
        file_get_contents($file_loc)
    )
);

return [
    'pdf' => $pdf
];