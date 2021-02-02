<?php

function load_class($class, $ext = false){
	if(!class_exists($class)){
		if($ext == false){
			include_once ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class.'.$class.'.php';
		}else{
			include_once ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'. DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR .'class'.DIRECTORY_SEPARATOR.'class.'.$class.'.php';
		}
	}
}

/*
Name: Stephen Kennedy
Date: 12/4/19
Comment: This is the core function behind the CMV structure of Vogon. It solves one of the things I hated about seeing this structure in OpenCart.

By including the documents in this fashion, each document is treated as a dynamically assigned function definition. The app doesn't have to load anything but the controllers, models, and views that are called by the route logic, meaning we only really have to slow the app down when we're doing slow tasks.

But the biggest benefit, and the thing I keep raving to myself about, is how clean it makes the sub-files. Many of them are only a few lines long, and it's all procedural programming, so it's very easy to write and follow.

The biggest downside is the additional mental overhead of keeping track of the logic of what is being called. Instead of getting a clear idea of the logic of a piece of code, you have to cross-reference several documents. In my experience with it, the positives have far outweighed the negatives, as breaking tasks into small reusable chunks of code has helped me to sub-divide tasks that seem to be initially complex into a series of simple actions that can be independently debugged.
*/
function loader($file, $data = []){
	global $db;
	foreach($data as $var => $val){
		$$var = $val;
	}
	if(file_exists($file)){ //this way if we call a controller that doesn't exist, we don't error
		return include $file;
	}else{
		//echo 'unable to load '.$file;
		//trigger_error('No file located at: '.$file, E_USER_NOTICE);
		return null; //Should probably look into returning some kind of non-exception style error, just so the app can give the user some feedback on why their request didn't follow the app's pre-programmed logic.
	}
}

function load_view($view, $data = [], $ext = false){
	if($view === 'null'){
		return null;
	}
	ob_start();
	if($ext == false){
		loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'view.'.$view.'.php', $data);
	}else{
		loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'view.'.$view.'.php', $data);
	}
	return ob_get_clean();
}
function load_model($model, $data = [], $ext = false){
	if($model === 'null'){
		return null;
	}
	if($ext == false){
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'model.'.$model.'.php', $data);
	}else{
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'model.'.$model.'.php', $data);
	}
}
function load_controller($controller, $data = [], $ext = false){
	if($controller === 'null'){
		return null;
	}
	if($ext == false){
		
		$load = ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'controller.'.$controller;
	}else{
		$load = ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'controller.'.$controller;
	}
	/*
	Name: Stephen Kennedy
	Date: 2/2/2021
	Comment: In order to better enforce the best practice of POST REDIRECT GET on our forms, this function is now going to attempt to load special controller.[controller_name].post.php files if a $_POST array is detected. Then, once that processing is done, it will redirect to the same slug, clearing the $_POST array.
	
	If developers need to redirect elsewhere, the redirect function kills PHP processing, so you just need to use it in your .post code.
	*/
	
	if(!empty($_POST)){
		$load_check = loader($load.'.post.php', $data);
		if($load_check === null){ //Hard test to ensure the file doesn't exist
			//This is a fallback for if the file doesn't exist so that we don't break compatibility with extensions written before this standard was introduced.
			return loader($load.'.php', $data);
		}else{
			redirect($_SERVER['REQUEST_URI']);
		}
	}else{
		return loader($load.'.php', $data);
	}
}

function debug_var($var){
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}
function dir_contents($dir, $ext_filter = false){
	$return = array_diff(scandir($dir), ['.', '..']);
	if($ext_filter != false){
		foreach($return as $key => $entry){
			$len = strlen($ext_filter) * -1;
			if(substr($entry, $len) != $ext_filter){
				unset($return[$key]);
			}
		}
		//Sort because we culled entries
	}
	sort($return);
	return $return;
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
/*
Name: Stephen Kennedy
Date: 10/5/19
Comment: Frequently, we're parsing the URI to determine what actions the controller will take, but since this whole system is build around the idea that slugs and slug_lengths are variable and easily changed by the user, we need a systemic way to get parts of the slug to enable the same functionality without manually parsing.
*/
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

/*
Name: Stephen Kennedy
Date: 10/5/19
Comment: By the same token, we need an easy way to build a slug.
*/
function build_slug($uri, $params = [], $ext = false){
	global $db;
	$string = URI;
	if($ext != false){
		$sql = 'SELECT * FROM route WHERE route_ext = :ext AND ext_primary = 1';
		$db_params = [':ext' => $ext];
		$query = $db->query($sql, $db_params);
		if($query != false){
			$result = $query->fetch();
			$string .= '/'. $result['route_slug'];
		}
	}
	$safe = [];
	$string .= '/'.$uri;
	if(count($params) > 0){
		foreach($params as $key => $value){
			if(gettype($value) == 'string'){
				$safe[] = urlencode($key) . '=' . urlencode(trim($value));
			}
		}
		$safe = implode('&', $safe);
		$string .= '?'.$safe;
	}
	return $string;
}

function get_var($var_name, $format="string"){
	global $db;
	$sql = "SELECT var_content FROM var WHERE var_name = :name";
	$params = [':name' => $var_name];
	$query = $db->query($sql, $params);
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
	$query = $db->query($sql, $params);
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
		$db->query($sql, $params);
	}else{
		$sql = 'INSERT INTO var (var_session, var_name, var_content, user_key) VALUES (';
		if($session == true){
			$sql .= ':session';
		}else{
			$sql .= '0';
		}
		$sql .= ', :name, :content, 0)';
		$check = $db->query($sql, $params);
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

function nice_date($date, $hours = true){
	if(gettype($date) != 'int'){
		$date = strtotime($date);
	}
	if($hours == true){
		return date('m/d/Y g:ia', $date);
	}else{
		return date('m/d/Y', $date);
	}
}
function db_date($date){
	if(gettype($date) != 'int'){
		$date = strtotime($date);
	}
	return date('Y-m-d H:i:s', $date);
}

function debug_d($var){
	echo '<pre>';
	print_r($var);
	echo '</pre>';
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
	$hours = floor(($seconds / 3600) % 60);
	
	$seconds = $seconds - ($hours * 3600);
	
	$minutes = floor(($seconds / 60) % 60);
	
	$seconds = $seconds - ($minutes * 60);
	
	$seconds = $seconds % 60;
	
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
?>