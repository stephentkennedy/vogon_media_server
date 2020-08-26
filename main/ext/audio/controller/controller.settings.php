<?php
if(empty($method)){ //Settings controllers should never be accessed directly, but loaded by the settings module, so if we set an access method, then we shouldn't do anything.
	switch($mode){
		case 'get_form':
		
			return load_view('settings', [], 'audio');
		
			break;
		case 'save':
			if(empty($_GET['form'])){
				break;
			}		
			switch($_GET['form']){
				case 'audioviz':
				
					load_model('save_vis', [], 'audio');
				
					break;
				case 'audiohistime':
				
					load_model('save_his_time', [], 'audio');
				
					break;
			}
			break;
	}
}