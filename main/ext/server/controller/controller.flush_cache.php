<?php
load_controller('header', [
	'title' => 'Flush Cache'
]);
echo load_view('flush_cache', [], 'server');
load_controller('footer');