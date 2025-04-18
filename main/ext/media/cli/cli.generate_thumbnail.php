<?php
set_time_limit(0);

$cli->line('Generating thumbnail(s)...');

$dir = $cli->get_flag('dir', './');
$dir = $cli->dir_string_to_path($dir);
$dir = rtrim($dir, '/');

$f = $cli->get_flag('file', null);

$skip_if_exists = true;

$replace = $cli->get_flag('replace', false);
if($replace){
    $skip_if_exists = false;
}

if($f === null){
    load_class('filesystem');
    $fs = new filesystem;
    //Scan our files
    $files = $fs->recursiveScan($dir, true);
    foreach($files as $f){
        $mime = mime_content_type($f);
        if(stristr($mime, 'video') === false){
            continue;
        }

        $sql = 'SELECT * FROM data WHERE data_content = :content AND ( data_type = "video" OR data_type = "tv" )';
        $params = [':content' => $f];
        $query = $db->t_query($sql, $params);
        if($query == false){
            ob_start();
            debug_d($db->error);
            return $message . ob_get_clean();
        }

        $result = $query->fetch();
        if($result == false){
            $cli->line('File not imported, skipping: '.$f);
            continue;
        }

        $cli->line('Running for: '.$f);
        //continue;

        load_model('update_thumbnail', [
            'skip_if_exists' => $skip_if_exists,
            'seconds' => 65,
            'id' => $result['data_id']
        ], 'media');
    }
}