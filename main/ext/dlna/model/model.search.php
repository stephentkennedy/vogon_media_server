<?php
// Injected: $container_id, $search_criteria, $start (int), $count (int, 0=all)

load_class('db_handler');
$dh = \dlna\media::get_handler();
$items = [];

if (trim($search_criteria) === '') {
    return ['items' => [], 'total' => 0];
}

$options     = ['meta' => ['mime_type', 'filesize', 'srt_subtitles', 'desc']];
$recognised  = true;
$clauses     = preg_split('/\s+and\s+/i', trim($search_criteria));

foreach ($clauses as $clause) {
    $clause = trim($clause);
    if ($clause === '') {
        continue;
    }
    if (preg_match('/upnp:class\s+derivedfrom\s+"object\.item\.videoItem"/i', $clause)) {
        $options['type'] = ['video', 'tv'];
    } elseif (preg_match('/upnp:class\s+derivedfrom\s+"object\.item\.audioItem"/i', $clause)) {
        $options['type'] = 'audio';
    } elseif (preg_match('/dc:title\s+contains\s+"([^"]+)"/i', $clause, $m)) {
        $options['search_name'] = '%' . $m[1] . '%';
    } else {
        $recognised = false;
        break;
    }
}

if (!$recognised || (!isset($options['type']) && !isset($options['search_name']))) {
    return ['items' => [], 'total' => 0];
}

$records = $dh->getRecords($options);

foreach ($records as $r) {
    $type = $r['data_type'];
    if ($type === 'video') {
        $r['dlna_id'] = 'item:v:' . $r['data_id'];
    } elseif ($type === 'tv') {
        $r['dlna_id'] = 'item:tv:' . $r['data_id'];
    } elseif ($type === 'audio') {
        $r['dlna_id'] = 'item:t:' . $r['data_id'];
    } else {
        continue;
    }
    $r['dlna_type'] = 'item';
    $items[] = $r;
}

$total = count($items);
if ($start > 0 || $count > 0) {
    $items = array_slice($items, $start, $count ?: null);
}

return ['items' => $items, 'total' => $total];
