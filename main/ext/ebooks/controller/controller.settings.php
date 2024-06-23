<?php
if(empty($method)){ //Settings controllers should never be accessed directly, but loaded by the settings module, so if we set an access method, then we shouldn't do anything.
    switch($mode){
        case 'get_form':
            return load_view('settings', [], 'ebooks');
            break;
        case 'save':
            if(empty($_GET['form'])){
                break;
            }
            switch($_GET['form']){
                case 'resizecbz':
                    load_model('save_resize_cbz', [], 'ebooks');
                    break;
            }
    }

}