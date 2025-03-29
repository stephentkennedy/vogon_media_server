<?php
if(!defined('NEW')){
	die('The installer is not directly accessed');
}
/*
Name: Steph Kennedy
Date: 2/17/21
Comment: We now allow the installer to be run from the command line so we can install and configure via bash script.
*/
if(isset($argc)){
	$options = getopt(null, [
		'app_name:',
		'database_host:',
		'database_name:',
		'database_user:',
		'database_password:',
		'uri:'
	]);
	foreach($options as $name => $value){
		$_POST[$name] = $value;
	}
	$cgi = true;
}else{
	$cgi = false;
}

if(!isset($_POST['app_name'])){
?>
<html>
	<head>
		<title>Installer</title>
		<link rel="stylesheet" href="css/layout.css">
		<link rel="stylesheet" href="css/media_server.css">
	</head>
	<body>
		<div id="content">
			<h1>Vogon Web Dev Tools Installer</h1>
			<p>Please fill out the items below so that vogon can be correctly installed to your system</p>
			<form method="post">
				<label for="app_name">App Name</label>
				<input type="text" name="app_name" value="Vogon Media Server">
				<label for="database_host">Database Host</label>
				<input type="text" name="database_host" value="localhost">
				<label for="database_name">Database Name</label>
				<input type="text" name="database_name">
				<label for="database_user">Database User</label>
				<input type="text" name="database_user">
				<label for="database_password">Database Password</label>
				<input type="text" name="database_password">
				<label for="uri">URI</label>
				<input type="text" name="uri" value="<?php echo rtrim(str_replace('/index.php', '', $_SERVER['REQUEST_URI']), '/'); ?>">
				<button type="submit">Install</button>
			</form>
		</div>
	</body>
</html>
<?php
}else{

	//We'll need this later
	define('ROOT', __DIR__);

	if(empty($_POST['uri'])){
		$_POST['uri'] = '';
	}
	
	define('URI', $_POST['uri']);

	$app_name = addslashes($_POST['app_name']);
	$uri = $_POST['uri'];
	$db_host = addslashes($_POST['database_host']);
	$db_name = addslashes($_POST['database_name']);
	$db_user = addslashes($_POST['database_user']);
	$db_pass = addslashes($_POST['database_password']);
	
	$ver_file = ROOT . DIRECTORY_SEPARATOR . 'ver';
	if(file_exists($ver_file)){
		$ver = file_get_contents($ver_file);
	}else{
		$ver = '0';
	}
	
$config = <<<HERE
[app_constants]
name = "{$app_name}"
ver = {$ver}
uri = "{$uri}"
[database]
driver = mysql
host = "{$db_host}"
name = "{$db_name}"
user = "{$db_user}"
pass = "{$db_pass}"
HERE;

	echo '<div style="background-color: #444; color: #fff; overflow: auto;">';

	file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'config.ini', $config);

	include __DIR__ . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'auto' . DIRECTORY_SEPARATOR . 'class.thumb.php';

	$db = new thumb(__DIR__ . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'config.ini');

	$table_data = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR . 'tables.json');
	$table_data = json_decode($table_data, true);

	foreach($table_data as $table => $data){
		$sql = $data['create'];
		$query = $db->t_query($sql);
		if($query == false){
			die('Unable to build '. $table .' table. Check database user permissions and run installer again.');
		}

		foreach($data['records'] as $r){
			$params = [];
			$columns = [];
			$param_keys = [];
			
			foreach($r as $column => $value){
				if(!is_numeric($column)){
					$columns[] = '`'.$column.'`';
					$param_keys[] = ':'.$column;
					$params[':'.$column] = $value;
				}
			}
			
			$sql = 'INSERT INTO `'.$table.'` (';
			$sql .= implode(', ', $columns);
			$sql .= ') VALUES (';
			$sql .= implode(', ', $param_keys);
			$sql .= ')';
			echo '<pre>';
			var_dump($sql);
			echo '</pre>';
			echo '<pre>';
			var_dump($params);
			echo '</pre>';
			$query = $db->t_query($sql, $params);
			var_dump($query);
			if($query == false){
				echo $db->error.'<br>';
			}
		}
	}
	echo '</div>';
	
	include __DIR__ . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'functions.php';
	
	//Build navigation
	load_model('rebuild_nav', ['type' => 'head']);
	load_model('rebuild_nav', ['type' => 'foot']);
	
	/*
	Name: Steph Kennedy
	Date: 9/25/2020
	Comment: The below code is how we used to generate menu navigation, however it's a little out of date now. By including the functions.php, we should be able to use the rebuild_nav model and just reuse the same code we use for the settings menu. If that works, the below code will be removed.
	*/
	
	/*$sql = 'SELECT * FROM route WHERE in_h_nav = 1';
	$query = $db->query($sql);
	$nav_routes = $query->fetchAll();
	$string = '';
	foreach($nav_routes as $r){
		$string .= '<a href="'.$uri.'/'.$r['route_slug'].'">';
		if(empty($r['nav_display'])){
			$string .= $r['route_ext'];
		}else{
			$string .= $r['nav_display'];
		}
		$string .= '</a> ';
	}
	$sql = 'UPDATE var SET var_content = :content WHERE var_name = "header_menu"';
	$params = [
		':content' => $string
	];
	$db->query($sql, $params);
	
	//Build footer navigation
	$sql = 'SELECT * FROM route WHERE in_f_nav = 1';
	$query = $db->query($sql);
	$nav_routes = $query->fetchAll();
	$string = '';
	foreach($nav_routes as $r){
		$string .= '<a href="'.$uri.'/'.$r['route_slug'].'">';
		if(empty($r['nav_display'])){
			$string .= $r['route_ext'];
		}else{
			$string .= $r['nav_display'];
		}
		$string .= '</a> ';
	}
	$sql = 'UPDATE var SET var_content = :content WHERE var_name = "footer_menu"';
	$params = [
		':content' => $string
	];
	$db->query($sql, $params);*/
	
	load_model('add', [
		'password' => '',
		'email' => 'Admin',
		'role' => 0
	], 'user');
	
	//Make upload DIR
	if(!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'upload')){
		mkdir(__DIR__ . DIRECTORY_SEPARATOR . 'upload');
	}
	$base = __DIR__ . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
	$thumb = $base . 'thumbs';
	$video = $base . 'video';
	$audio = $base . 'audio';
	if(!file_exists($thumb)){
		mkdir($thumb);
	}
	if(!file_exists($video)){
		mkdir($video);
	}
	if(!file_exists($audio)){
		mkdir($audio);
	}
	
	
	//Disable installer
	unlink(__DIR__ . DIRECTORY_SEPARATOR . 'new_install');
	unlink($ver_file);

	if($cgi == false){
		redirect(build_slug('settings'));
	}
}