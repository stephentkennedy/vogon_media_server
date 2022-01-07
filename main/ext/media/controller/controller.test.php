<?php
$clerk = new clerk;

$metas = [
	'length',
	'poster'
];

$options = [
	'metas' => $metas,
	'type' => 'video',
	'parent' => 11676,
	'orderby' => 'length'
];

$records = $clerk->getRecords($options);

debug_d($records);