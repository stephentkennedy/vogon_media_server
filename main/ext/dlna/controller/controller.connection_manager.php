<?php
load_class('soap', 'dlna');
$cm_ns  = 'urn:schemas-upnp-org:service:ConnectionManager:1';
$parsed = \dlna\soap::parse();
header('Content-Type: text/xml; charset=utf-8');

switch ($parsed['action']) {
    case 'GetProtocolInfo':
        $source = implode(',', [
            'http-get:*:video/mp4:*',
            'http-get:*:audio/mpeg:*',
            'http-get:*:audio/aac:*',
            'http-get:*:audio/flac:*',
            'http-get:*:audio/x-flac:*',
            'http-get:*:image/jpeg:*',
        ]);
        echo \dlna\soap::response($cm_ns, 'GetProtocolInfo', ['Source' => $source, 'Sink' => '']);
        break;
    case 'GetCurrentConnectionIDs':
        echo \dlna\soap::response($cm_ns, 'GetCurrentConnectionIDs', ['ConnectionIDs' => '0']);
        break;
    case 'GetCurrentConnectionInfo':
        echo \dlna\soap::response($cm_ns, 'GetCurrentConnectionInfo', [
            'RcsID'                 => '-1',
            'AVTransportID'         => '-1',
            'ProtocolInfo'          => '',
            'PeerConnectionManager' => '',
            'PeerConnectionID'      => '-1',
            'Direction'             => 'Output',
            'Status'                => 'OK',
        ]);
        break;
    default:
        echo \dlna\soap::fault('401', 'Invalid Action');
}
