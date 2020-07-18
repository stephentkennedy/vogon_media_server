<?php
//$u_dir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
$u_dir = URI . '/upload/';
$video = $u_dir . 'THE_MATRIX_RELOADED_DISC_1_t00.mp4';
//$video = URI . build_slug('/matrix-reloaded', [], 'media');
$mime_type = 'video/mp4';
$poster = $u_dir . 'poster.jpg';
$video_data = [
	'video' => $video,
	'mime_type' => $mime_type,
	'poster' => $poster
];
load_controller('header', ['view' => 'mini']);
echo load_view('video', $video_data, 'media');
load_controller('footer', ['view' => 'mini']);