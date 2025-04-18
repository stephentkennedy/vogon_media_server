<?php
// Injected: $object_id, $browse_flag, $start (int), $count (int, 0=all)

load_class('db_handler');
$dh = \dlna\media::get_handler();
$items = [];

switch (true) {
    case $object_id === '0':
        $items = [
            ['dlna_type' => 'container', 'title' => 'Video', 'id' => '1'],
            ['dlna_type' => 'container', 'title' => 'Music', 'id' => '4'],
        ];
        break;

    case $object_id === '1':
        $items = [
            ['dlna_type' => 'container', 'title' => 'Movies', 'id' => '2'],
            ['dlna_type' => 'container', 'title' => 'Series', 'id' => '3'],
        ];
        break;

    case $object_id === '2':
        $records = $dh->getRecords([
            'type'   => 'video',
            'parent' => 0,
            'meta'   => ['mime_type', 'filesize', 'length', 'poster', 'director', 'release', 'srt_subtitles', 'desc'],
        ]);
        foreach ($records as $r) {
            $r['dlna_type'] = 'item';
            $r['dlna_id']   = 'item:v:' . $r['data_id'];
            $items[] = $r;
        }
        break;

    case $object_id === '3':
        $records = $dh->getRecords(['type' => 'series', 'meta' => ['poster']]);
        foreach ($records as $r) {
            $r['dlna_type'] = 'container';
            $r['title']     = $r['data_name'];
            $r['id']        = 'ser:' . $r['data_id'];
            $items[] = $r;
        }
        break;

    case strpos($object_id, 'ser:') === 0:
        $series_id = (int) substr($object_id, 4);
        $records = $dh->getRecords([
            'type'         => 'season',
            'parent'       => $series_id,
            'meta'         => ['season_ord', 'poster'],
            'orderby'      => 'meta_season_ord',
            'orderby_int'  => true,
        ]);
        foreach ($records as $r) {
            $r['dlna_type'] = 'container';
            $r['title']     = $r['data_name'];
            $r['id']        = 'sea:' . $series_id . ':' . $r['data_id'];
            $items[] = $r;
        }
        $mixed = $dh->getRecords([
            'type' => ['tv', 'video'],
            'parent'  => $series_id,
            'meta'    => ['season', 'mime_type', 'filesize', 'poster', 'release', 'srt_subtitles', 'desc'],
        ]);
        $has_uncategorized = false;
        foreach ($mixed as $r) {
            if ($r['data_type'] === 'video') {
                $r['dlna_type'] = 'item';
                $r['dlna_id']   = 'item:v:' . $r['data_id'];
                $items[] = $r;
            } elseif ($r['data_type'] === 'tv' && empty($r['season'])) {
                $has_uncategorized = true;
            }
        }
        if ($has_uncategorized) {
            array_unshift($items, [
                'dlna_type' => 'container',
                'title'     => 'Uncategorized',
                'id'        => 'sea:' . $series_id . ':0',
            ]);
        }
        break;

    case strpos($object_id, 'sea:') === 0:
        // sea:{sid}:{season_id} — fetch all episodes for the series, filter to this season in PHP
        $parts     = explode(':', $object_id);
        $series_id = (int) $parts[1];
        $season_id = (int) $parts[2];
        $records = $dh->getRecords([
            'type'        => ['tv', 'video'],
            'parent'      => $series_id,
            'meta'        => ['mime_type', 'filesize', 'length', 'season', 'episode_ord', 'poster', 'srt_subtitles', 'desc'],
            'orderby'     => 'meta_episode_ord',
            'orderby_int' => true,
        ]);
        foreach ($records as $r) {
            if ((int) $r['season'] !== $season_id) {
                continue;
            }
            $r['dlna_type'] = 'item';
            if ($r['data_type'] === 'video') {
                $r['dlna_id']   = 'item:v:' . $r['data_id'];
            }else{
                $r['dlna_id']   = 'item:tv:' . $r['data_id'];
            }
            $items[] = $r;
        }
        break;

    case $object_id === '4':
        $records = $dh->getRecords(['type' => 'album']);
        foreach ($records as $r) {
            $r['dlna_type'] = 'container';
            $r['title']     = $r['data_name'];
            $r['id']        = 'alb:' . $r['data_id'];
            $items[] = $r;
        }
        break;

    case strpos($object_id, 'alb:') === 0:
        $album_id = (int) substr($object_id, 4);
        $records = $dh->getRecords([
            'type'   => 'audio',
            'parent' => $album_id,
            'meta'   => ['mime_type', 'filesize', 'length', 'artist', 'track'],
        ]);
        foreach ($records as $r) {
            $r['dlna_type'] = 'item';
            $r['dlna_id']   = 'item:t:' . $r['data_id'];
            $items[] = $r;
        }
        break;
}

$total = count($items);
if ($start > 0 || $count > 0) {
    $items = array_slice($items, $start, $count ?: null);
}

return ['items' => $items, 'total' => $total];
