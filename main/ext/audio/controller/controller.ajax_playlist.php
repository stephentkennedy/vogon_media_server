<?php
$playlist = load_model('get_songs', ['songs' => $_GET['songs']], 'audio');
$playlist['current'] = $_GET['current'];
echo load_view('ajax_playlist', $playlist, 'audio');