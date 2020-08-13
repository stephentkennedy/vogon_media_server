<?php
$dir = ROOT;
$dir_data = load_model('dir_scan', ['dir' => $dir], 'filebrowser');
$preloaded = load_view('dir', $dir_data, 'filebrowser');
echo load_view('ajax_filebrowse', ['preload' => $preloaded, 'form' => true, 'dir' => ROOT], 'filebrowser');