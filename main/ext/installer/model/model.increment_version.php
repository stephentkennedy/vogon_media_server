<?php
load_class('vParse');
$v = new vParse;
$new_version = $v->increment($level);

$ini_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'config.ini';

$settings = [];
$settings = parse_ini_file($ini_file, true);

$settings['app_constants']['ver'] = $new_version;

load_model('write_ini', [
	'ini' => $settings,
	'filename' => $ini_file
], 'installer');

unset($settings);

return $new_version;