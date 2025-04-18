<?php

if(
    !isset($argc)
    || file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'new_install')
){
    header('Location: /');
}

define('SKIP_ROUTER', true);

include __DIR__.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'bootstrap.php';

load_class('cli');
$cli = new cli;

load_cli('router', [
    'cli' => $cli
]);