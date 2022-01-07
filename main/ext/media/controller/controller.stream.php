<?php
$video_slug = get_slug_part(1);
/*
Name: Stephen Kennedy
Date: 5/25/20
Comment: Once we have a database we'll do the lookup here, for now we're just hardcoding our result.
*/

$u_dir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;

$video = $u_dir . 'THE_MATRIX_RELOADED_DISC_1_t00.mp4';

load_class('VideoStream', 'media');
$stream = new VideoStream($video);
$stream->start();