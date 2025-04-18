<?php
load_class('media', 'dlna');
load_class('xml_builder', 'dlna');


$action = get_slug_part(1);

$upload_dir = ROOT . '/upload/';
$log_dir = $upload_dir . 'xml_logging';
if(!file_exists($log_dir)){
    mkdir($log_dir);
}

$request_uri = $_SERVER['REQUEST_URI'];
$log_filename = slugify($request_uri).'.xml';
$request = file_get_contents('php://input');
file_put_contents($log_dir . '/' . $log_filename, $request);

if(empty($action)){
    $options = [];
    if(!empty($_SESSION['dlna_friendly_name'])){
        $options['friendly_name'] = $_SESSION['dlna_friendly_name'];
    }
    if(!empty($_SESSION['dlna_bound_ip'])){
        $options['bound_ip'] = $_SESSION['dlna_bound_ip'];
    }
    $root = new \dlna\root_xml_builder($options);
    $output = $root->output();

    header('Content-Type: text/xml');
    echo $output;
    return;
}

switch($action){
    //Load our XML definitions
    case 'cmScpd.xml':
        $xml_loc = ext_root('dlna') . 'xml_src' . DIRECTORY_SEPARATOR . 'cmScpd.xml';
        $xml_content = file_get_contents($xml_loc);
        header('Content-Type: text/xml');
        echo $xml_content;
        break;
    case 'cdScpd.xml':
        $xml_loc = ext_root('dlna') . 'xml_src' . DIRECTORY_SEPARATOR . 'cdScpd.xml';
        $xml_content = file_get_contents($xml_loc);
        header('Content-Type: text/xml');
        echo $xml_content;
        break;
    case 'upnp_av.wsdl':
        $wsdl_loc = ext_root('dlna') . 'wsdl' . DIRECTORY_SEPARATOR . 'upnp_av.wsdl';
        $wsdl_content = file_get_contents($wsdl_loc);
        $wsdl_content = str_replace('{{content_dir_url}}', '/dir', $wsdl_content);
        header('Content-Type: text/xml');
        echo $wsdl_content;
        break;

    //Pass our specific requests to the controllers
    case 'con':
        load_controller('connection_manager', [], 'dlna');
        break;
    case 'dir':
        load_controller('content_directory', [], 'dlna');
        break;

    case 'icon':
        $icon_file = ROOT . '/upload/favicon.png';
        $icon_mime = mime_content_type($icon_file);
        $icon = file_get_contents($icon_file);
        header('Content-Type: '.$icon_mime);
        echo $icon;
        break;

    case 'stream':
        load_class('db_handler');
        $item_id = get_slug_part(2);
        $parts   = explode(':', $item_id);
        $data_id = isset($parts[2]) ? (int)$parts[2] : 0;
        if (!$data_id) { http_response_code(404); return; }

        $dh     = \dlna\media::get_handler();
        $record = $dh->getRecord(['id' => $data_id, 'meta' => ['mime_type', 'filesize']]);
        $media  = new \dlna\media($record);

        if (!$media->mime || !$media->filename) { http_response_code(404); return; }

        header('Accept-Ranges: bytes');
        header('transferMode.dlna.org: Streaming');
        header('contentFeatures.dlna.org: DLNA.ORG_OP=01;DLNA.ORG_FLAGS=01500000000000000000000000000000');

        if (!empty($_SESSION['dlna_xsendfile'])) {
            header('Content-Type: '   . $media->mime);
            header('Content-Length: ' . $media->filesize);
            header('X-Sendfile: '     . $media->filename);
            return;
        }

        // Close the framework's output buffer so chunks go directly to the client
        while (ob_get_level()) ob_end_clean();
        set_time_limit(0);

        $filesize = (int)$media->filesize;
        $start = 0;
        $end   = $filesize - 1;
        if (isset($_SERVER['HTTP_RANGE'])) {
            preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $m);
            $start = (int)$m[1];
            if (!empty($m[2])) $end = (int)$m[2];
            http_response_code(206);
            header("Content-Range: bytes $start-$end/$filesize");
        }
        $length = $end - $start + 1;
        header('Content-Type: '   . $media->mime);
        header('Content-Length: ' . $length);
        $fp = fopen($media->filename, 'rb');
        fseek($fp, $start);
        $remaining = $length;
        while ($remaining > 0 && !feof($fp)) {
            $chunk = fread($fp, min(8192, $remaining));
            echo $chunk;
            $remaining -= strlen($chunk);
        }
        fclose($fp);
        break;

    case 'subtitle':
        load_class('db_handler');
        $item_id = get_slug_part(2);
        $parts   = explode(':', $item_id);
        $data_id = isset($parts[2]) ? (int)$parts[2] : 0;
        if (!$data_id) { http_response_code(404); return; }

        $dh     = \dlna\media::get_handler();
        $record = $dh->getRecord(['id' => $data_id, 'meta' => ['srt_subtitles']]);

        $srt_path = $record['srt_subtitles'] ?? '';

        if (empty($srt_path)) {
            // Lazy discovery: look for a .srt sibling of the video file
            $video_path = $record['data_content'] ?? '';
            if (!empty($video_path) && !file_exists($video_path)) {
                $video_path = \url2dir($video_path);
            }
            if (!empty($video_path) && file_exists($video_path)) {
                $candidate = preg_replace('/\.[^.]+$/', '.srt', $video_path);
                if (file_exists($candidate)) {
                    $srt_path = $candidate;
                    $dh->child->addRecord([
                        'data_id'           => $data_id,
                        'data_meta_name'    => 'srt_subtitles',
                        'data_meta_content' => $srt_path,
                    ]);
                }
            }
        } elseif (!file_exists($srt_path)) {
            $srt_path = \url2dir($srt_path);
        }

        if (empty($srt_path) || !file_exists($srt_path)) { http_response_code(404); return; }

        header('Content-Type: text/srt; charset=utf-8');
        header('Content-Length: ' . filesize($srt_path));
        readfile($srt_path);
        break;
}