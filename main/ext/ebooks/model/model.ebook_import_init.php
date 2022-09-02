<?php
set_time_limit(0);

load_class('filesystem');
$fs = new filesystem;

$files = $fs->recursiveScan($dir, true);
return $files;
