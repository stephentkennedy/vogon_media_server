<?php
load_class('ajax_loop_interface');
$ali = new ajax_loop_interface([
	'mode' => 'db',
	'sql' => 'SELECT * FROM data WHERE data_type = "video" OR data_type = "audio" ORDER BY `data_id`',
	'ext' => 'server',
	'model' => 'mark_orphan_entries',
]);