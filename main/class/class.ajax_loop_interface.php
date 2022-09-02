<?php
/*
Developer: Steph Kennedy
Date: 3/3/21
Comment: This is to allow us an easy interface for doing looped tasks via ajax so we can have a clean updating output & progress bars rather than just sitting and waiting for things to load, and without abusing output flushing so we don't have to wait for things like the footer to load.
*/
class ajax_loop_interface {
	private $base_sql = false;
	private $count_sql = false;
	private $params = [];
	private $debug = false;
	private $offset = true;
	private $model;
	private $ext = false;
	private $var_name = 'row';
	public $error;
	public $db = false;
	
	public function __construct($options){
		switch($options['mode']){
			case 'db':
				$check = $this->db_mode($options);
				break;
			case 'session_array':
				$check = $this->sa_mode($options);
				break;
		}
		if($check === false){
			$output = [
				'state' => 'error',
				'continue' => false,
				'error' => $this->error
			];
			echo load_view('json', $output);
		}
	}
	
	private function sa_mode($options){
		if(empty($options['init_model'])){
			$this->error = 'There is no provided model to initialize the session array. Check that it is at the key "init_model"';
			return false;
		}
		if(empty($options['model'])){
			$this->error = 'There was no provided model for processing the data';
			return false;
		}
		$this->model = $options['model'];
		if(!empty($options['ext'])){
			$this->ext = $options['ext'];
		}
		if(!empty($options['var_name'])){
			$this->var_name = $options['var_name'];
		}
		if(!isset($_GET['offset'])){
			$init_data = [];
			if(!empty($options['init_data'])){
				$init_data = $options['init_data'];
			}
			$_SESSION['ajax_loop_interface_array'] = load_model($options['init_model'], $init_data, $this->ext);
			$count = count($_SESSION['ajax_loop_interface_array']);
			$output = [
				'total_tasks' => $count,
				'state' => 'initialized',
				'next_q' => '?offset=0&count='.$count,
				'message' => 'Starting...',
				'continue' => true
			];
			echo load_view('json', $output);
		}else{
			$offset = (int)$_GET['offset'];
			if(!empty($_SESSION['ajax_loop_interface_array'][$offset])){
				$model_output = load_model($this->model, [
					$this->var_name => $_SESSION['ajax_loop_interface_array'][$offset]
				], $this->ext);
				$output = [
					'message' => $model_output
				];
				$output['next'] = $offset + 1;
				if($offset >= (int)$_GET['count'] - 1){
					$output['state'] = 'finished';
					$output['continue'] = false;
					unset($_SESSION['ajax_loop_interface_array']);
					if(isset($options['cleanup'])){
						$output['cleanup'] = load_model($options['cleanup'], [], $this->ext);
					}
				}else{
					$output['state'] = 'working';
					$output['continue'] = true;
					$output['next_q'] = '?offset='. ($offset + 1).'&count='.$_GET['count'];
				}
			}else{
				//Clean up after ourselves;
				unset($_SESSION['ajax_loop_interface_array']);
				$output = [
					'state' => 'finished',
					'continue' => false
				];
				if(isset($options['cleanup'])){
					$output['cleanup'] = load_model($options['cleanup'], [], $this->ext);
				}
			}
			echo load_view('json', $output);
		}
	}
	
	private function db_mode($options){
		if(empty($options['db']) || !($options['db'] instanceof thumb)){
			global $db;
			if(empty($db) || !($db instanceof thumb)){
				$this->error = 'There is no global Thumb in $db to attach, and no valid Thumb instance was provided in the options.';
				return false;
			}
			$this->db = $db;
		}else{
			$this->db = $options['db'];
		}
		
		if(isset($options['offset'])){
			$this->offset = $options['offset'];
		}
		
		
		if(empty($options['sql'])){
			$this->error = 'There was no provided SQL select statement';
			return false;
		}
		
		if(empty($options['model'])){
			$this->error = 'There was no provided model for processing the data';
			return false;
		}
		$this->model = $options['model'];
		if(!empty($options['ext'])){
			$this->ext = $options['ext'];
		}
		if(!empty($options['var_name'])){
			$this->var_name = $options['var_name'];
		}
		
		
		$check = $this->parse($options['sql']);
		if($check == false){
			$this->error = 'The provided SQL statement was not a simple select statement. Check you\'re SQL syntax and that you are not trying to pass more than one statement at a time.';
			return false;
		}
		
		if(!empty($options['params'])){
			$this->params = $options['params'];
		}
		if(!isset($_GET['offset'])){
			$query = $this->db->t_query($this->count_sql, $this->params);
			if($query == false){
				ob_start();
				debug_d($this->db->error);
				$error = ob_get_clean();
				$this->error = $error;
				return false;
			}
			$count = $query->fetch()['count'];
			$output = [
				'total_tasks' => $count,
				'state' => 'initialized',
				'next_q' => '?offset=0&count='.$count,
				'message' => 'Starting...',
				'continue' => true
			];
			echo load_view('json', $output);
		}else{
			$offset = (int)$_GET['offset'];
			if($this->offset === true){
				$offset_sql = $this->build_sql($offset);
			}else{
				$offset_sql = $this->base_sql;
			}
			if($offset_sql == false){
				$this->error = 'Non-numeric Offset provided.';
				return false;
			}
			$query = $this->db->t_query($offset_sql, $this->params);
			if($query == false){
				ob_start();
				debug_d($this->db->error);
				$error = ob_get_clean();
				$this->error = $error;
				return false;
			}else{
				$result = $query->fetch();
				$model_output = load_model($this->model, [
					$this->var_name => $result
				], $this->ext);
				$output = [
					'message' => $model_output
				];
				if($this->debug == true){
					ob_start();
					debug_d($result);
					$output['row'] = ob_get_clean();
					$output['sql'] = $offset_sql;
				}
				if($offset >= $_GET['count'] - 1){
					$output['state'] = 'finished';
					$output['continue'] = false;
					if(isset($options['cleanup'])){
						$output['cleanup'] = load_model($options['cleanup'], [], $this->ext);
					}
				}else{
					$output['state'] = 'working';
					$output['continue'] = true;
					$output['next_q'] = '?offset='. ($offset + 1).'&count='.$_GET['count'];
				}
				echo load_view('json', $output);
			}
		}
	}
	
	private function parse($sql){
		if(strtolower(substr($sql, 0, strlen('select'))) != 'select'){
			//If it's not a select statement we don't want to do anything.
			return false;
		}
		if(stristr(substr($sql, 0, -1), ';') !== false){
			//If it's ending the statement early, or a compound statement we don't want to do anything.
			return false;
		}
		$temp = strtolower($sql);
		$split = explode('from', $temp);
		$initial = strlen($split[0]);
		$headless = substr($sql, $initial);
		$count_sql = 'SELECT count(*) as `count` '.$headless;
		$this->count_sql = $count_sql;
		$this->base_sql = $sql;
		return true;
	}
	
	private function build_sql($offset){
		if(is_numeric($offset)){
			$string = $this->base_sql . ' LIMIT '.$offset.',1';
			return $string;
		}else{
			return false;
		}
	}
}