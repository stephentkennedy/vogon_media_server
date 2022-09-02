<?php
$id = $_POST['id'];
$url = $_POST['url'];

$url = str_replace(build_slug('upload/thumbs/'), '', $url);

$clerk = new clerk;

$metas = [
    'poster' => $url
];

$check = $clerk->updateMetas($id, $metas);