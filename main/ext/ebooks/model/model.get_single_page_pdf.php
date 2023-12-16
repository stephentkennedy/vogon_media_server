<?php
//Setup
require_once ROOT . '/vendor/autoload.php';

$new_pdf = new setasign\Fpdi\Fpdi();

$new_pdf->addPage();
$new_pdf->setSourceFile($source);
$new_pdf->useTemplate($new_pdf->importPage($page));

$pdf_string = $new_pdf->Output('S', '');

return [
    'pdf' => $pdf_string
];

?>