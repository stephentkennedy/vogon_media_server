<?php

if(empty($method)){
    switch($mode){
        case 'get_form':
            $rss_timeout = get_var('rss_timeout');
            if(empty($rss_timeout)){
                $rss_timeout = 5;
            }
            return load_view('settings', [
                'rss_timeout' => $rss_timeout
            ], 'rss_reader');
            break;
        case 'save':
            if(empty($_GET['form'])){
                break;
            }
            switch($_GET['form']){
                case 'timeout':
                    load_model('save_timeout', [], 'rss_reader');
                    break;
            }
            break;
    }
}