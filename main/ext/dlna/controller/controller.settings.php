<?php

if(empty($method)){
    switch($mode){
		case 'get_form':
		
			return load_view('settings', [], 'dlna');
		
			break;
		case 'save':
			if(empty($_GET['form'])){
				break;
			}		
			switch($_GET['form']){
				case 'main_dlna':
				
					load_model('save_settings', [], 'dlna');
				
					break;
			}
			break;
	}
}