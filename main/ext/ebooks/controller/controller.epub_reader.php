<?php
$reader_dir = '/vendor/epub_reader/';
$assets = [
    build_style_tag($reader_dir . 'css/normalize.css'),
    build_style_tag($reader_dir . 'css/main.css'),
    build_style_tag($reader_dir . 'css/popup.css'),
    '<script type="text/javascript" src="'.build_slug('/js/jquery.min.js').'"></script>',
    build_script_tag($reader_dir . 'js/libs/zip.min.js'),
    build_script_tag($reader_dir . 'js/libs/screenhul.min.js'),
    build_script_tag($reader_dir . 'js/epub.js'),
    build_script_tag($reader_dir . 'js/reader.js')
];

load_controller('header', [
    'head_tags' => $assets,
    'view' => 'nano'
]);

$url = load_model('file_to_url', ['item' => $item], 'ebooks')['url'];

echo load_view('epub_reader', [
    'item' => $item,
    'file_url' => $url
], 'ebooks');

load_controller('footer', [
    'view' => 'mini'
]);