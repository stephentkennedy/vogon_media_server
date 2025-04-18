<?php
set_time_limit(0);

$cli->line('Importing Videos...');

$dir = $cli->get_flag('dir', './');
$cli->line($dir);
$dir = $cli->dir_string_to_path($dir);
$dir = rtrim($dir, '/');

$cli->line('Importing from: '.$dir);
$check = $cli->get_user_input('Is this correct? (y/N):');

if(!empty($check) && strtolower(substr($check, 0, 1)) == 'y'){
    $files = load_model('import_init', ['dir' => $dir], 'media');
}else{
    $cli->error('Process canceled by user.');
}

$cli->line(count($files).' file found. Starting...');

foreach($files as $f){
    //$cli->line($f);
    $message = load_model('import', ['f' => $f], 'media');
    $message = str_replace('<br>', PHP_EOL, $message);
    $cli->line($message);
}

$cli->success('Import loop complete.');