<?php
load_controller('header', [
	'title' => 'Data table cleanup.'
]);
echo load_view('reorder_data_table', [], 'server');
load_controller('footer');