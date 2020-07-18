<?php
class thumb extends PDO{
	public $error = false;
	public $settings = [];
	public $last;
	public function __construct($file = __DIR__.DIRECTORY_SEPARATOR.'config.ini'){
		$settings = parse_ini_file($file, true);
		if($settings == false){
			die('Unable to read settings file.');
		}
		$dns = $settings['database']['driver'].':host='.$settings['database']['host'];
		if(!empty($settings['database']['port'])){
			$dns .= ';port='.$settings['database']['port'];
		}
		$dns .= ';dbname='.$settings['database']['name'];
		parent::__construct($dns, $settings['database']['user'], $settings['database']['pass']);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->settings = $settings;
	}
	
	public function query($sql, $params = []){
		//Always safe rather than sorry
		$this->beginTransaction();
		$trans = $this->prepare($sql);
		//$trans = $this->binder($trans, $params);
		try{
			$trans->execute($params);
			$this->last = $this->lastInsertId();
			$this->commit();
			return $trans;
		}catch(PDOException $e){
			$this->rollBack();
			$this->error = $e;
			return false;
		}
	}
	
	//This function allows us to cast ints as ints, rather than the default PDO prepared behavior of strings. This is useful for Limits and Offsets
	public function binder($trans, $params){
		foreach($params as $key => $value){
			if(is_int($value)){
				$trans->bindParam($key, $value, PDO::PARAM_INT);
			}else{
				$trans->bindParam($key, $value, PDO::PARAM_STR);
			}
		}
		return $trans;
	}
	
	public function fromFile($file){
		$handle = fopen($file, 'r');
		$pattern = '/{;}($|\n|\r)/i';
		if($handle){
			$cmd = '';
			while(($line = fgets($handle)) !== false){
				if(preg_match($pattern, $line) === 1){
					$cmd .= $line;
					$this->query(str_replace('{;}', ';', $cmd), []);
					$cmd = '';
				}else{
					$cmd .= $line;
				}
			}
		}
	}
	
	public function export($file, $table, $data = false){
		$handle = fopen($file, 'w');
		$header = '/* Exported by the Thumb Database Class in the Vogon Framework, this file is not compatible with standard MySQL imports without replacing the follow character combination "{;}" => ";" */
		';
		fwrite($handle, $header);
		if(gettype($table) == 'string'){
			$sql = "SHOW CREATE TABLE `".$table."`";
			$query = $this->query($sql, []);
			$create = $query->fetch()['Create Table'];
			if($data == false){
				$create = preg_replace('/AUTO_INCREMENT=[0-9]+ /', 'AUTO_INCREMENT=0', $create);
				fwrite($handle, PHP_EOL.$create.';'.PHP_EOL);
			}
			switch(gettype($data)){
				case 'array':
					fwrite($handle, PHP_EOL.PHP_EOL);
					$sql = "SHOW KEYS FROM `".$table."` WHERE Key_name = 'PRIMARY'";
					$query = $this->query($sql, []);
					$primary = $query->fetch()['Column_name'];
					foreach($data as $id){
						$sql = "SELECT * FROM `".$table."` WHERE `".$primary."` = :id";
						$params = [":id" => $id];
						$query = $this->query($sql, $params);
						$line = $query->fetch();
						$keys = [];
						$values = [];
						foreach($line as $key => $value){
							$keys[] = '`'.$key.'`';
							$values[] = "'".addslashes($value)."'";
						}
						$keys = implode(', ', $keys);
						$values = implode(', ', $values);
						$sql = "INSERT INTO `".$table."` (".$keys.") VALUES (".$values."){;}";
						fwrite($handle, $sql.PHP_EOL);
					}
					break;
				default:
					if($data == true){
						$sql = "SELECT * FROM `".$table."`";
						$rows = $this->query($sql, []);
						fwrite($handle, PHP_EOL.PHP_EOL);
						foreach($rows as $row){
							$keys = [];
							$values = [];
							foreach($row as $key => $value){
								if(!is_numeric($key)){
									$keys[] = '`'.$key.'`';
									$values[] = "'".addslashes($value)."'";
								}
							}
							$keys = implode(', ', $keys);
							$values = implode(', ', $values);
							$sql = "INSERT INTO `".$table."` (".$keys.") VALUES (".$values."){;}";
							fwrite($handle, $sql.PHP_EOL);
						}
					}
					break;
			}
		}else{
			$tables = $table;
			foreach($tables as $table){
				$sql = "SHOW CREATE TABLE `".$table."`";
				$query = $this->query($sql, []);
				$create = $query->fetch()['Create Table'];
				if($data[$table] == false){
					$create = preg_replace('/AUTO_INCREMENT=[0-9]+ /', 'AUTO_INCREMENT=0 ', $create);
				}
				fwrite($handle, PHP_EOL.$create.'{;}'.PHP_EOL);
				switch(gettype($data[$table])){
					case 'array':
						fwrite($handle, PHP_EOL.PHP_EOL);
						$sql = "SHOW KEYS FROM `".$table."` WHERE Key_name = 'PRIMARY'";
						$query = $this->query($sql, []);
						$primary = $query->fetch()['Column_name'];
						foreach($data[$table] as $id){
							$sql = "SELECT * FROM `".$table."` WHERE `".$primary."` = :id";
							$params = [":id" => $id];
							$query = $this->query($sql, $params);
							$line = $query->fetch();
							$keys = [];
							$values = [];
							foreach($line as $key => $value){
								$keys[] = '`'.$key.'`';
								$values[] = "'".addslashes($value)."'";
							}
							$keys = implode(', ', $keys);
							$values = implode(', ', $values);
							$sql = "INSERT INTO `".$table."` (".$keys.") VALUES (".$values."){;}";
							fwrite($handle, $sql.PHP_EOL);
						}
						break;
					default:
						if($data == true){
							$sql = "SELECT * FROM `".$table."`";
							$rows = $this->query($sql, []);
							fwrite($handle, PHP_EOL.PHP_EOL);
							foreach($rows as $row){
								$keys = [];
								$values = [];
								foreach($row as $key => $value){
									if(!is_numeric($key)){
										$keys[] = '`'.$key.'`';
										$values[] = "'".addslashes($value)."'";
									}
								}
								$keys = implode(', ', $keys);
								$values = implode(', ', $values);
								$sql = "INSERT INTO `".$table."` (".$keys.") VALUES (".$values."){;}";
								fwrite($handle, $sql.PHP_EOL);
							}
						}
						break;
				}
			}
		}
		return fclose($handle);
	}
}
?>