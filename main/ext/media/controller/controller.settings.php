<?php

if(empty($method)){ //Settings controllers should never be accessed directly, but loaded by the settings module, so if we set an access method, then we shouldn't do anything.
	switch($mode){
		case 'get_form':
		
			return load_view('settings', [], 'media');
		
			break;
		case 'save':
		
			if(empty($_GET['form'])){
				break;
			}		
			switch($_GET['form']){
				case 'thumb':
				
					load_model('save_thumb_dir', [], 'media');
				
					break;
				case 'mediahistime':
				
					load_model('save_his', [], 'media');
				
					break;
				case 'minidlna':
				
					load_model('save_minidlna', [], 'media');
				
					break;
			}
		
			break;
	}
}