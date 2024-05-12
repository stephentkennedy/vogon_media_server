<?php

class tmdb_wrapper{
    public $api = false;

    function __construct(){
        $api_key = get_var('tmdb_api_key');
        if(empty($api_key)){
            return false;
        }

        include_once __DIR__ . '/vendor/tmdb/tmdb-api.php';

        $this->api = new TMDB();
        $this->api->setAPIKey($api_key);
    }
}