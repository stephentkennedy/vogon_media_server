<?php
	if(empty($method)){ //Settings controllers should never be accessed directly, but loaded by the settings module, so if we set an access method, then we shouldn't do anything.
		switch($mode){
			case 'get_form':
			
				if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'search' && !empty($_REQUEST['search'])){
					$routes = load_model('search', [
						'search_query' => $_REQUEST['search'],
						'tables' => ['route'],
						'links' => []
					]);
				}else{
					
					$routes = load_model('search', [
						'search_query' => '',
						'tables' => ['route'],
						'links' => []
					]);
				}
				$themes = dir_contents(ROOT . DIRECTORY_SEPARATOR . 'css');
				$active_theme = str_replace('css/', '' , $_SESSION['css']);
			
				return load_view('settings', [
					'routes' => $routes,
					'themes' => $themes,
					'active_theme' => $active_theme
				]);
				break;
			case 'save':
				if(empty($_GET['form'])){
					break; //Shouldn't be hitting this case, but this fixes it
				}
				switch($_GET['form']){
					case 'route_remove':
						load_model('remove_route', [
							'id' => $_GET['route_id']
						]);
						break;
					case 'route_toggle_h':
						load_model('toggle_route_nav', [
							'id' => $_GET['route_id'],
							'type' => 'head'
						]);
						break;
					case 'route_toggle_f':
						load_model('toggle_route_nav', [
							'id' => $_GET['route_id'],
							'type' => 'foot'
						]);
						break;
					case 'route_toggle_m':
						load_model('toggle_route_main', ['id' => $_GET['route_id']]);
						break;
					case 'route':
						load_model('add_route', [
							'slug' => $_POST['slug'],
							'controller' => $_POST['controller'],
							'ext' => $_POST['ext']
						]);
						break;
					case 'theme':
						load_model('change_theme', [
							'theme' => $_POST['theme']
						]);
						break;
					case 'change_display_name':
						load_model('change_display_name', [
							'id' => $_POST['id'],
							'display_name' => $_POST['display_name']
						]);
						break;
					case 'rebuild_nav':
						load_model('rebuild_nav', ['type' => 'head']);
						load_model('rebuild_nav', ['type' => 'foot']);
						break;
					case 'error_reporting':
						load_model('save_error', []);
						break;
					case 'background_video':
						load_model('save_bg_vid', []);	
						break;
				}
				break;
			case 'install_ext':
				load_controller('ext_install', ['extension' => $_GET['ext']]);
				break;
				
		}
	}else if($method == 'ajax'){
		switch($_GET['action']){
			case 'form':
				$model_data = load_model($_GET['form']);
				echo load_view($_GET['form'], $model_data);
				break;
		}
	}
?>