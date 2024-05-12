<?php

debug_d('Here');
error_reporting(E_ALL);

load_class('tmdb_wrapper');

$tmdb = new tmdb_wrapper;

if($tmdb->api == false){
	debug_d('No API Key');
	die();
}

$records = $tmdb->api->searchMovie('The Matrix');

debug_d($records);

debug_d('Done');