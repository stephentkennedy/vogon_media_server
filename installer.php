<?php
if(!defined('NEW')){
	die('The installer is not directly accessed');
}
if(!isset($_POST['app_name'])){
?>
<html>
	<head>
		<title>Installer</title>
		<link rel="stylesheet" href="css/layout.css">
		<link rel="stylesheet" href="css/dev.css">
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

	$app_name = addslashes($_POST['app_name']);
	$uri = $_POST['uri'];
	$db_host = addslashes($_POST['database_host']);
	$db_name = addslashes($_POST['database_name']);
	$db_user = addslashes($_POST['database_user']);
	$db_pass = addslashes($_POST['database_password']);
	
$config = <<<HERE
[app_constants]
name = "{$app_name}"
ver = 0.5a
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
		$query = $db->query($sql);
		if($query == false){
			die('Unable to build '. $table .' table. Check database user permissions and run installer again.');
		}
		foreach($data['records'][$table] as $r){
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
			$query = $db->query($sql, $params);
			var_dump($query);
			if($query == false){
				echo $db->error.'<br>';
			}
		}
	}
	echo '</div>';
	
	//Build header navigation
	$sql = 'SELECT * FROM route WHERE in_h_nav = 1';
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
	$db->query($sql, $params);
	
	//Make upload DIR
	mkdir(__DIR__ . 'upload');
	
	//Disable installer
	unlink(__DIR__ . DIRECTORY_SEPARATOR . 'new_install');

	echo '<script type="text/javascript">window.location = "'.$uri.'/settings";</script>';
}