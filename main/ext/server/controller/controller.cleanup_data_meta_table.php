<?php
load_controller('header', [
	'title' => 'Data Meta table cleanup.'
]);
echo load_view('reorder_data_meta_table', [], 'server');
load_controller('footer');