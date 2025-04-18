<?php
// soap is not pre-loaded by controller.main.php; xml_builder and media are.
load_class('soap', 'dlna');

$cd_ns       = 'urn:schemas-upnp-org:service:ContentDirectory:1';
$parsed      = \dlna\soap::parse();
$bound_ip    = $_SESSION['dlna_bound_ip'] ?? $_SERVER['SERVER_ADDR'];
$stream_base = 'http://' . $bound_ip . build_slug('/dlna/stream/');

header('Content-Type: text/xml; charset=utf-8');

// Returns [xml_string, item_count] — count comes from the builder so items that
// fail the mime check (no file, unsupported format) don't inflate NumberReturned.
function _build_didl(array $items, string $parent_id, string $stream_base, string $bound_ip): array {
    $builder = new \dlna\xml_builder($parent_id);
    foreach ($items as $item) {
        if ($item['dlna_type'] === 'container') {
            $folder = $builder->addFolder($item['title'], $item['id']);
            if (!empty($item['poster'])) {
                $folder->addAlbumArtUri('http://' . $bound_ip . \dir2url($item['poster']));
            }
        } else {
            $media = new \dlna\media($item);
            if (!$media->mime) {
                continue;
            }
            // item_definition is only set by identify_format's non-cached path; guard here
            if (empty($media->item_definition)) {
                $type = $item['data_type'] ?? '';
                if ($type === 'video' || $type === 'tv') {
                    $media->item_definition = 'object.item.videoItem';
                } elseif ($type === 'audio') {
                    $media->item_definition = 'object.item.audioItem';
                } else {
                    $media->item_definition = 'object.item';
                }
            }
            $node = $builder->addItem($media, $item['dlna_id']);
            if (!empty($media->meta['description'])) {
                $node->addDescription($media->meta['description']);
            }
            // filesize is also only set by the non-cached path; null is safe for addResource
            $node->addResource($stream_base . $item['dlna_id'], $media->mime, $media->filesize ?? null, null);
            if (!empty($media->icon)) {
                $node->addAlbumArtUri('http://' . $bound_ip . $media->icon);
            }
            if (!empty($item['srt_subtitles'])) {
                $node->addResource('http://' . $bound_ip . build_slug('/dlna/subtitle/') . $item['dlna_id'], 'text/srt');
            }
        }
    }
    return [$builder->getXml(), $builder->count];
}

switch ($parsed['action']) {
    case 'Browse':
        $object_id   = $parsed['args']['ObjectID']        ?? '0';
        $browse_flag = $parsed['args']['BrowseFlag']      ?? 'BrowseDirectChildren';
        $start       = (int)($parsed['args']['StartingIndex']  ?? 0);
        $count       = (int)($parsed['args']['RequestedCount'] ?? 0);

        $result             = load_model('browse', compact('object_id', 'browse_flag', 'start', 'count'), 'dlna');
        [$didl, $returned]  = _build_didl($result['items'], $object_id, $stream_base, $bound_ip);

        echo \dlna\soap::response($cd_ns, 'Browse', [
            'Result'         => $didl,
            'NumberReturned' => (string) $returned,
            'TotalMatches'   => (string) $result['total'],
            'UpdateID'       => '1',
        ]);
        break;

    case 'GetSearchCapabilities':
        echo \dlna\soap::response($cd_ns, 'GetSearchCapabilities', ['SearchCaps' => 'dc:title,upnp:class']);
        break;

    case 'GetSortCapabilities':
        echo \dlna\soap::response($cd_ns, 'GetSortCapabilities', ['SortCaps' => '']);
        break;

    case 'GetSystemUpdateID':
        echo \dlna\soap::response($cd_ns, 'GetSystemUpdateID', ['Id' => '1']);
        break;

    case 'Search':
        $container_id    = $parsed['args']['ContainerID']    ?? '0';
        $search_criteria = $parsed['args']['SearchCriteria'] ?? '';
        $start           = (int)($parsed['args']['StartingIndex']  ?? 0);
        $count           = (int)($parsed['args']['RequestedCount'] ?? 0);

        $result            = load_model('search', compact('container_id', 'search_criteria', 'start', 'count'), 'dlna');
        [$didl, $returned] = _build_didl($result['items'], $container_id, $stream_base, $bound_ip);

        // Per UPnP spec, Search response wrapper is named BrowseResponse
        echo \dlna\soap::response($cd_ns, 'Browse', [
            'Result'         => $didl,
            'NumberReturned' => (string) $returned,
            'TotalMatches'   => (string) $result['total'],
            'UpdateID'       => '1',
        ]);
        break;

    default:
        echo \dlna\soap::fault('401', 'Invalid Action');
}
