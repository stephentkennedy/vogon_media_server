<?php
load_class('db_handler');
$d = new db_handler('data');

$search = [
	'type' => 'series',
	'groupby' => 'name'
];

$series = $d->getRecords($search);

return $series;