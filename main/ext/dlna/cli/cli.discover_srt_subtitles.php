<?php

load_class('db_handler');
$d   = new db_handler('data');
$dm  = new db_handler('data_meta');
$dwm = $d->meta_link($dm, [
    'meta_key_field'   => 'data_meta_name',
    'meta_value_field' => 'data_meta_content'
]);

$rpp    = 20;
$offset = 0;

$search = [
    'type'          => ['video', 'tv'],
    'meta'             => ['srt_subtitles'],
    'meta_srt_subtitles' => null,
    'limit'            => $rpp,
    'offset'           => $offset,
    'orderby'          => 'id'
];

$records = $dwm->getRecords($search);

if (empty($records) && $dwm->total_count === 0) {
    $cli->line('No records without srt_subtitles found.');
    return;
}

$cli->line($dwm->total_count . ' records to process');

foreach ($records as $item) {
    $result = load_model('discover_srt', [
        'data_id'      => $item['data_id'],
        'data_content' => $item['data_content'],
    ], 'dlna');
    $cli->line(($result ? 'Found:    ' : 'No SRT:   ') . $item['data_content']);
}

while ($offset <= $dwm->total_count) {
    $offset += $rpp;
    $search['offset'] = $offset;
    $records = $dwm->getRecords($search);
    foreach ($records as $item) {
        $result = load_model('discover_srt', [
            'data_id'      => $item['data_id'],
            'data_content' => $item['data_content'],
        ], 'dlna');
        $cli->line(($result ? 'Found:    ' : 'No SRT:   ') . $item['data_content']);
    }
}

$cli->success('Done');
