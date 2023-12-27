<?php
if(empty($record)){
    return [
        'link' => false,
        'error' => 'No valid link in the database exists for this file. ' . $search
    ];
}
$type = $record['data_type'];

$ext = '';

switch($type){
    case 'video':
    case 'tv':
        $ext = 'media';
        break;
    case 'audio':
        $ext = 'audio';
        break;
    case 'pdf':
    case 'cbz':
    case 'epub':
        $ext = 'ebooks';
        break;
    default:
        $ext = false;
        break;
}

if(!empty($ext)){
    $relative_slug = '';
    $params = [];
    switch($ext){
        case 'media':
            $relative_slug = 'watch/'.$record['data_id'];
            break;
        case 'audio':
            $relative_slug = '';
            $params = [
                's' => $record['data_content']
            ];
            break;
        case 'ebooks':
            $relative_slug = 'view/'.$record['data_id'];
            break;
    }
    $link = build_slug($relative_slug, $params, $ext);
    return [
        'link' => $link,
        'record' => $record
    ];
}else{
    return [
        'link' => false,
        'error' => 'No valid link in the database exists for this file. ' . $search,
        'record' => $record
    ];
}