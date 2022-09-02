<?php
echo load_view('ajax_scan', [
	'route' => 'ajax/ajax_audio_import/audio?dir=' . urlencode($dir)
], 'audio');