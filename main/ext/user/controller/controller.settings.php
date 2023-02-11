<?php
if(empty($method)){
    switch($mode){
        case 'get_form':

            $roles = load_model('get_user_roles', [], 'user');

            return load_view('settings', [
                'roles' => $roles
            ], 'user');

            break;
        case 'save':
            if(empty($_GET['form'])){
                break;
            }
            switch($_GET['form']){
                case 'user_roles':
                    load_model('save_user_roles', [], 'user');
                    break;
            }
            break;
    }
}