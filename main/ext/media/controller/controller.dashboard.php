<?php

$recent = load_model('get_recent', [], 'media');
$recent['items'] = load_model('get_random', $recent, 'media');
load_controller('header', ['title' => 'Media Server']);
echo load_view('dashboard', $recent, 'media');
load_controller('footer');