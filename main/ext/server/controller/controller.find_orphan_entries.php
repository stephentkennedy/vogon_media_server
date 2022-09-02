<?php
load_controller('header', [
	'title' => 'Find Orphan Entries'
]);
echo load_view('find_orphan_entries', [], 'server');
load_controller('footer');