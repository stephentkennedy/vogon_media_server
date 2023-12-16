<?php
$reader_dir = '/js/epub_reader/';
$assets = [
    build_style_tag($reader_dir . 'css/normalize.css'),
    build_style_tag($reader_dir . 'css/main.css'),
    build_style_tag($reader_dir . 'css/popup.css'),
    //'<script type="text/javascript" src="'.build_slug('/js/jquery.min.js').'"></script>',
    build_script_tag($reader_dir . 'js/libs/zip.min.js'),
    build_script_tag($reader_dir . 'js/epub.js'),
];

load_controller('header', [
    'head_tags' => $assets,
    'view' => 'mini'
]);

$url = load_model('file_to_url', ['item' => $item], 'ebooks')['url'];

echo load_view('epub_reader.v2', [
    'item' => $item,
    'file_url' => $url
], 'ebooks');

load_controller('footer', [
    'view' => 'mini'
]);