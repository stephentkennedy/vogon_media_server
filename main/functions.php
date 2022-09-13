<?php

//Vogon Core Functions
function load_file($file, $parameters = []){
	global $db;
	foreach($parameters as $var => $val){
		$$var = $val;
	}
	if(file_exists($file)){
		$data = $parameters; //For compatibility, in future updates this will be removed.
		return include $file;
	}else{
		return null;
	}
}

function load_class($class, $ext = false){
	if(!class_exists($class)){
		if($ext == false){
			$class_file = ROOT . DIRECTORY_SEPARATOR . 'main' .DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class.' . $class . '.php';
		}else{
			$class_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR .'class'. DIRECTORY_SEPARATOR . 'class.' . $class . '.php';
		}
		if(file_exists($class_file)){
			load_file($class_file);
		}
	}
}

function load_view($view, $parameters = [], $ext = false){
	if($view === 'null'){
		return null;
	}
	ob_start();
	if($ext == false){
		$view_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'view.' . $view . '.php';
	}else{
		$view_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'view.' . $view . '.php';
	}
	load_file($view_file, $parameters);
	return ob_get_clean();
}

function load_model($model, $parameters = [], $ext = false){
	if($model === 'null'){
		return null;
	}
	if($ext == false){
		$model_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'model.' . $model . '.php';
	}else{
		$model_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'model.' . $model . '.php';
	}
	return load_file($model_file, $parameters);
}

function load_controller($controller, $data = [], $ext = false){
	if($controller === 'null'){
		return null;
	}
	if($ext == false){
		$controller_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'controller.' . $controller;
	}else{
		$controller_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'controller.' . $controller;
	}
	/*
	Name: Steph Kennedy
	Date: 2/2/2021
	Comment: In order to better enforce the best practice of POST REDIRECT GET on our forms, this function is now going to attempt to load special controller.[controller_name].post.php files if a $_POST array is detected. Then, once that processing is done, it will redirect to the same slug, clearing the $_POST array.
	
	If developers need to redirect elsewhere, the redirect function kills PHP processing, so you just need to use it in your .post code.
	*/
	
	if(!empty($_POST)){
		$load_check = load_file($controller_file.'.post.php', $data);
		if($load_check === null){ //Hard test to ensure the file doesn't exist
			//This is a fallback for if the file doesn't exist so that we don't break compatibility with extensions written before this standard was introduced.
			return load_file($controller_file.'.php', $data);
		}else{
			redirect($_SERVER['REQUEST_URI']);
		}
	}else{
		return load_file($controller_file.'.php', $data);
	}
}

//General Functions
function debug_d($var){ //Compatibility shim, new calls should be to debug_dump();
	debug_dump($var);
}

function debug_dump($var){
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function dir_contents($dir, $extension_filter = false){
	$remove_from_results = [
		'.',
		'..'
	];
	$directory_contents = scandir($dir);
	$to_return = array_diff($directory_contents, $remove_from_results);
	if($extension_filter != false){
		filter_array_of_filenames_by_extension($to_return, $extension_filter);
	}
	sort($to_return);
	return $to_return;
}

function filter_array_of_filenames_by_extension($items, $extension){
	foreach($items as $key => $entry){
		if(!compare_extension($entry, $extension)){
			unset($items[$key]);
		}
	}
	return $items;
}

function compare_extension($filename, $extension){
	$len = strlen($extension) * -1;
	if(substr($filename, $len) != $extension){
		return false;
	}
	return true;
}

function array_diff_exti($array1, $array2){
	$temp_array1 = [];
	foreach($array1 as $key => $value){
		$temp = explode('.', $value);
		array_pop($temp);
		$temp = implode('.', $temp);
		$temp_array1[$key] = $temp;
	}
	$temp_array2 = [];
	foreach($array2 as $key => $value){
		$temp = explode('.', $value);
		array_pop($temp);
		$temp = implode('.', $temp);
		$temp_array2[$key] = $temp;
	}
	$temp_array = array_diff($temp_array1, $temp_array2);
	$return_array = [];
	foreach($temp_array as $key => $value){
		$return_array[$key] = $array1[$key];
	}
	return $return_array;
}

function get_slug_part($requested = 'ext', $slug = false){
	if($slug == false){
		$slug = $_SERVER['REQUEST_URI'];
	}
	$slug = explode('?', $slug)[0];
	$slug = str_replace(URI, '', $slug);
	if($requested == 'ext'){
		$requested = 0;
	}
	$slug = ltrim($slug, '/');
	$slug = explode('/', $slug);
	if(isset($slug[(int) $requested])){
		return $slug[(int) $requested];
	}else{
		return false;
	}
}

function build_slug($uri, $params = [], $ext = false){
	$slug_to_return = URI;
	if($ext != false){
		$ext_slug = get_ext_slug($ext);
		if(!empty($ext_slug)){
			$slug_to_return .= '/'. $ext_slug;
		}
	}
	$slug_to_return .= '/'.ltrim($uri, '/');
	if(count($params) > 0){
		$slug_to_return .= urlencode_get_values($params);
	}
	return $slug_to_return;
}

function get_ext_slug($ext){
	global $db;
	$sql = 'SELECT * FROM route WHERE route_ext = :ext AND ext_primary = 1';
	$db_params = [':ext' => $ext];
	$query = $db->t_query($sql, $db_params);
	if($query != false){
		return $query->fetch()['route_slug'];
	}else{
		return false;
	}
}

function urlencode_get_values($array){
	$encoded_items_array = [];
	foreach($array as $key => $value){
		$encoded_key = urlencode($key);
		$encoded_value = urlencode($value);
		$encoded_items_array[] = $encoded_key . '=' . $encoded_value;
	}
	$encoded_items_string = implode('&', $encoded_items_array);
	$encoded_items_string = '?'.$encoded_items_string;
	return $encoded_items_string;
}

function get_var($var_name, $format="string"){
	global $db;
	$sql = "SELECT var_content FROM var WHERE var_name = :name";
	$params = [':name' => $var_name];
	$query = $db->t_query($sql, $params);
	$r = $query->fetch();
	$content = $r['var_content'];
	switch($format){
		case 'array':
			$content = unserialize($content);
			break;
		default:
			break;
	}
	return $content;
}

function put_var($var_name, $value, $format="string", $session = false){
	global $db;
	$sql = "SELECT var_content FROM var WHERE var_name = :name";
	$params = [':name' => $var_name];
	$query = $db->t_query($sql, $params);
	if($query != false){
		$query = $query->fetch();
	}
	switch($format){
		case 'array':
			$content = serialize($content);
			break;
		default:
			break;
	}
	$params = [
		':content' => $value,
		':name' => $var_name
	];
	if($session == true){
		$params[':session'] = true;
	}
	if($query != false){
		$sql = 'UPDATE var SET var_content = :content';
		if($session == true){
			$sql .= ', var_session = :session';
		}
		$sql .= ' WHERE var_name = :name';
		$db->t_query($sql, $params);
	}else{
		$sql = 'INSERT INTO var (var_session, var_name, var_content, user_key) VALUES (';
		if($session == true){
			$sql .= ':session';
		}else{
			$sql .= '0';
		}
		$sql .= ', :name, :content, 0)';
		$check = $db->t_query($sql, $params);
		if($check == false){
			debug_d($db->error);
		}
	}
	if($session == true){
		$_SESSION[$var_name] = $value;
	}
}

function slugify($content){
	$content = trim($content);
	$content = preg_replace('/[^a-zA-Z0-9]/', '_', $content);
	$content = preg_replace('/_{2,}/', '_', $content);
	$content = trim($content, '_');
	return strtolower($content);
}

function nice_date($date = ''){
	return reformat_date($date, 'm/d/Y g:ia');
}

function db_date($date = ''){
	return reformat_date($date, 'Y-m-d H:i:s');
}

function reformat_date($date, $format){
	if(gettype($date) != 'int'){
		$date = strtotime($date);
	}
	return date($format, $date);
}

function recursiveScan($loc, $all = false){
	$array = scandir($loc);
	$return = [];
	foreach($array as $file){
		if($file != '.' && $file != '..'){
			if(is_dir($loc. DIRECTORY_SEPARATOR .$file)){
				$newArray = recursiveScan($loc. DIRECTORY_SEPARATOR .$file, $all);
				foreach($newArray as $item){
					$return[] = $item;
				}
			}else{
				if($all == false){
					if(stristr($file, '.php') !== false){
						$return[] = $loc. DIRECTORY_SEPARATOR .$file;
					}
				}else{
					$return[] = $loc. DIRECTORY_SEPARATOR .$file;
				}
			}
		}
	}
	return $return;
}

function formatLength($seconds){
	//Added @ operators to suppress depreciated notices for lack of precision
	//Will probably have to rewrite this function before another php upgrade.
	$hours = @floor(($seconds / 3600) % 60);
	
	$seconds = $seconds - ($hours * 3600);
	
	$minutes = @floor(($seconds / 60) % 60);
	
	$seconds = $seconds - ($minutes * 60);
	
	$seconds = @($seconds % 60);
	
	$string = $seconds . 's';
	
	if($minutes > 0){
		$string = $minutes. 'm '.$string;
	}
	if($hours > 0){
		$string = $hours.'h '.$string;
	}
	return $string;
}

function redirect($loc){
	if(headers_sent()){
		echo '<script type="text/javascript"> window.location = "'.$loc.'"; </script>';
	}else{
		header('Location: '.$loc);
	}
	die();
}

function trueLoc($file){
	if(file_exists($file)){
		return $file;
	}
	if(stristr($file, ROOT) === false){
		if(substr($file, 0, 1) == DIRECTORY_SEPARATOR){
			$file = ROOT . $file;
		}else{
			$file = ROOT . DIRECTORY_SEPARATOR . $file;
		}
		if(file_exists($file)){
			return $file;
		}else{
			return false;
		}
	}
}

function ext_root($ext){
	return ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR;
}

function url2dir($url){
	$parts = parse_url($url);
	$path = str_replace(URI, ROOT, $parts['path']);
	return $path;
}

/*
Developer: Steph Kennedy
Date: 4/23/21
Comment: Hooks support code
*/
function register_filter($hook, $model, $ext = false){
	global $hooks;
	if(empty($hooks)){
		$hooks = [];
	}
	if(empty($hooks[$hook])){
		$hooks[$hook] = [];
	}
	$hooks[$hook][] = [
		'model' => $model,
		'ext' => $ext
	];
}

function run_filters($hook, $hook_data = []){
	global $hooks;
	if(!empty($hooks) && !empty($hooks[$hook])){
		foreach($hooks[$hook] as $h){
			$hook_data = load_model($h['model'], $hook_data, $h['ext']);
		}
	}
	return $hook_data;
}

function outputFilters($buffer){
	$returned = run_filters('output', ['buffer' => $buffer]);
	return $returned['buffer'];
}