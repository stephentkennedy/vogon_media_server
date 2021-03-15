<?php

umask(0007); //Remove execute permissions from any files that we generate with this extraction

mkdir(ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update');

$filename = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update.zip';

$zip = new ZipArchive();
$zip->open($filename);
$zip->extractTo(ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'update');
$zip->close();

return 'Extracted Files.';