<?php
//This file requires pdfinfo from xpdf to be installed.

$cmd = "pdfinfo";

$filepath = str_replace('"', '\"', $filepath);

$filename = '"'.$filepath.'"';

exec($cmd . ' ' . $filename, $output); //Save output to array of lines

return [
    'lines' => $output
];