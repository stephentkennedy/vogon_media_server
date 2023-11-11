<?php
//This is a data table handling class for Vogon. Its goal is to automate the data-table and data-meta operations for the vogon framework
class db_handler {
	public $params, $where, $sql;
	private $debug = false;
	public $query_mode = 'AND';
	public $depth = 0;
	public $db, $total_count; 
	public $table;
	public $meta_table = false;
	public $structure = [];
	public $primary_key = false;
	
	public function __construct($options = [
		'db' => false,
		'table' => 'data',
	]){
		if(!is_array($options)){
			$options = [
				'db' => false,
				'table' => $options
			];
		}
		
		if(empty($options['db'])){
			global $db;
		}else{
			$db = $options['db'];
		}
		$this->table = $options['table'];
		if(!empty($db)){
			$this->db = $db;
			$this->init();
		}else{
			die('instantiated without database object in $db variable.');
		}
	}
	
	private function init(){
		$table_name = $this->table;
		$safe_name = $table_name . '_';
		$sql = 'SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME` LIKE :table';
		$params = [
			':table' => $table_name
		];
		$query = $this->db->t_query($sql, $params);
		$results = $query->fetchAll();
		foreach($results as $column){
			$column_name = $column['COLUMN_NAME'];
			$column_type = $column['COLUMN_TYPE'];
			$column_pri = $column['COLUMN_KEY'];
			if($column_pri == 'PRI'){
				$this->primary_key = $column_name;
			}
			$friendly_name = str_replace($safe_name, '', $column_name);
			$this->structure[$friendly_name] = [
				'machine_name' => $column_name,
				'data_type' => $column_type
			];
		}
	}
	
	public function updateRecord($values, $id = false){
		if($id == false){
			return $this->addRecord($values);
		}
		if(empty($values) || gettype($values) != 'array'){
			return false;
		}
		$sql = 'UPDATE `'.$this->table.'` SET ';
		$this->where = [];
		$this->params = [];
		foreach($values as $key => $item){
			if(!empty($this->structure[$key]) && $this->structure[$key]['machine_name'] != $this->primary_key){
				$this->buildParam($item, $this->structure[$key]['machine_name'], $key);
			}
		}
		$set = implode(', ', $this->where);
		$sql .= $set . ' WHERE `'.$this->table.'`.`'.$this->primary_key.'` = :id';
		$this->params[':id'] = $id;
		$this->sql = $sql;
		return $this->db->t_query($sql, $this->params);
	}
	
	public function set_default_params(){
		$this->params = [];
		foreach($this->structure as $friendly => $object){
			if($object['machine_name'] != $this->primary_key){
				switch($object['data_type']){
					default:
						$value = 0;
						break;
					case 'text':
					case 'tinytext':
					case 'longtext':
					case 'varchar':
						$value = '';
						break;
					case 'date':
					case 'datetime':
					case 'timestamp':
						$value = date('Y-m-d H:i:s');
						break;
				}
				$this->params[':'.$friendly] = $value;
			}
		}
	}
	
	public function parse_select_options($options){		
		
		$search_command = 'search_';
		$s_len = strlen($search_command);
		
		$not_command = 'not_';
		$n_len = strlen($not_command);
		
		$in_command = 'in_';
		$in_len = strlen($in_command);
		
		$nin_command = 'not_in_';
		$nin_len = strlen($nin_command);
		
		$greater_command = 'greater_than_';
		$greater_len = strlen($greater_command);
		
		$less_command = 'less_than_';
		$less_len = strlen($less_command);
		
		foreach($options as $option_name => $value){
			if(!empty($this->structure[$option_name])){
				$this->buildParam($value, $this->structure[$option_name]['machine_name'], $option_name);
			}else if(substr($option_name, 0, $s_len) == $search_command){
				$name = substr($option_name, $s_len);
				if(empty($this->structure[$name])){
					continue;
				}
				$this->buildParam($value, $this->structure[$name]['machine_name'], $name, 'LIKE');
			}else if(substr($option_name, 0, $n_len) == $not_command){
				$name = substr($option_name, $n_len);
				if(empty($this->structure[$name])){
					continue;
				}
				$this->buildParam($value, $this->structure[$name]['machine_name'], $name, '!=');
			}else if(substr($option_name, 0, $in_len) == $in_command){
				$name = substr($option_name, $in_len);
				if(empty($this->structure[$name])){
					continue;
				}
				$this->buildParam($value, $this->structure[$name]['machine_name'], $name, 'IN');
			}else if(substr($option_name, 0, $nin_len) == $nin_command){
				$name = substr($option_name, $nin_len);
				if(empty($this->structure[$name])){
					continue;
				}
				$this->buildParam($value, $this->structure[$name]['machine_name'], $name, 'NOT IN');
			}else if(substr($option_name, 0, $greater_len) == $greater_command){
				$name = substr($option_name, $greater_len);
				if(empty($this->structure[$name])){
					continue;
				}
				$this->buildParam($value, $this->structure[$name]['machine_name'], $name, '>');
			}else if(substr($option_name, 0, $less_len) == $less_command){
				$name = substr($option_name, $less_len);
				if(empty($this->structure[$name])){
					continue;
				}
				$this->buildParam($value, $this->structure[$name]['machine_name'], $name, '<');
			}
		}
	}
	
	public function addRecord($values){
		$sql = 'INSERT INTO `'.$this->table.'` (';
		$temp = [];
		$friendly_temp = [];
		foreach($this->structure as $friendly => $object){
			if($object['machine_name'] != $this->primary_key){
				$temp[] = '`'.$object['machine_name'].'`';
				$friendly_temp[] = ':'.$friendly;
			}
		}
		$temp = implode(', ', $temp);
		$friendly_temp = implode(', ', $friendly_temp);
		$sql .= $temp .') VALUES ('.$friendly_temp.')';
		
		$this->set_default_params();
		foreach($values as $key => $item){
			if(!empty($this->structure[$key]) && $this->structure[$key]['machine_name'] != $this->primary_key){
				$this->buildParam($item, $this->structure[$key]['machine_name'], $key);
			}
		}
		$this->sql = $sql;
		$this->db->t_query($sql, $this->params);
		return $this->db->last;
	}
	
	public function getRecords($options){
		if(is_numeric($options)){
			return $this->getRecord(['id' => $options]);
		}
		if(isset($options['limit'])){
			$cql = 'SELECT COUNT(*) as `count`';
		}
		$sql = 'SELECT `'.$this->table.'`.*';
		if(isset($options['groupby']) && !empty($this->structure[$options['groupby']]['machine_name'])){
			$sql .= ', COUNT('.$this->structure[$options['groupby']]['machine_name'].') as "count" ';
		}
		if(!empty($options['self_join'])){
			$sql .= ', ';
			$sj = $this->selfJoin($options['self_join']);
			$sql .= implode(', ', $sj['select']). ' FROM `'.$this->table.'` '.implode(' ', $sj['joins']);
			if(!empty($cql)){
				$cql .= ', ';
				$cql .= implode(', ', $sj['select']). ' FROM `'.$this->table.'` '.implode(' ', $sj['joins']);
			}
			unset($options['self_join']);
		}else{
			$sql .= ' FROM `'.$this->table.'`';
			if(!empty($cql)){
				$cql .= ' FROM `'.$this->table.'`';
			}
		}
		$this->t_query_mode = 'AND';
		if(!empty($options['query_mode'])){
			$this->t_query_mode = $options['query_mode'];
			unset($options['query_mode']);
		}
		$this->where = [];
		$this->params = [];
		$this->depth = 0;
		$this->parse_select_options($options);
		if(isset($options['sub_query'])){
			$this->parse_where_statement($options['sub_query']);
		}
		if(count($this->where) > 0){
			$where = implode(' '. $this->t_query_mode .' ', $this->where);
			$sql .= ' WHERE '.$where;
			if(isset($cql)){
				$cql .= ' WHERE '.$where;
			}
		}
		if(isset($cql)){
			$query = $this->db->t_query($cql, $this->params);
			if(!empty($query)){
				$count = $query->fetch()['count'];
				$this->total_count = $count;
			}else{
				debug_d($sql);
				debug_d($cql);
				debug_d($this->params);
				debug_d($this->db->error);
				die();
			}
		}
		if(isset($options['orderby']) && !empty($this->structure[$options['orderby']])){
			$sql .= ' ORDER BY '.$this->structure[$options['orderby']]['machine_name'];
			
			if(!empty($options['orderby_int'])){
				$sql .= ' + 0';
			}
			if(!empty($options['orderby_dir'])){
				switch(strtolower($options['orderby_dir'])){
					default:
						$sql .= ' ASC';
						break;
					case 'desc':
						$sql .= ' DESC';
						break;
				}
			}
		}
		if(isset($options['groupby']) && !empty($this->structure[$options['groupby']])){
			$sql .= ' GROUP BY '.$this->structure[$options['groupby']]['machine_name'];
		}		
		if(isset($options['limit']) && is_numeric($options['limit'])){
			$sql .= ' LIMIT ';
			if(isset($options['offset']) && is_numeric($options['offset'])){
				$sql .= $options['offset'].',';
			}
			$sql .= $options['limit'];
			
		}
		
		$this->sql = $sql;

		if(
			isset($options['sql_only']) 
			&& $options['sql_only'] === true
		){
			return $sql;
		}

		$query = $this->db->t_query($sql, $this->params);
		if($query != false){
			$records = $query->fetchAll();
		}else{
			$records = [];
		}
		return $records;
	}
	
	public function getRecord($options){
		$sql = 'SELECT `'.$this->table.'`.*';
		if(!empty($options['self_join'])){
			$sql .= ', ';
			$sj = $this->selfJoin($options['self_join']);
			$sql .= implode(', ', $sj['select']). 'FROM `'.$this->table.'` '.implode(' ', $sj['joins']);
			unset($options['self_join']);
		}else{
			$sql .= ' FROM `'.$this->table.'`';
		}		
		$this->t_query_mode = 'AND';
		$this->where = [];
		$this->params = [];
		$this->depth = 0;
		if(is_numeric($options)){
			$options = [
				'id' => $options
			];
		}
		if(!empty($options['query_mode'])){
			$this->t_query_mode = $options['query_mode'];
			unset($options['query_mode']);
		}
		$this->parse_select_options($options);
		if(isset($options['sub_query'])){
			$this->parse_where_statement($options['sub_query']);
		}
		$sql .= ' WHERE ' . implode(' ' . $this->t_query_mode . ' ', $this->where);
		if(isset($options['orderby']) && !empty($this->structure[$options['orderby']])){
			$sql .= ' ORDER BY '.$this->structure[$options['orderby']]['machine_name'];
			
			if(!empty($options['orderby_int'])){
				$sql .= ' + 0';
			}
			if(!empty($options['orderby_dir'])){
				switch(strtolower($options['orderby_dir'])){
					default:
						$sql .= ' ASC';
						break;
					case 'desc':
						$sql .= ' DESC';
						break;
				}
			}
		}
		$this->sql = $sql;
		
		$query = $this->db->t_query($sql, $this->params);
		if($query != false){
			$records = $query->fetch();
		}else{
			$records = [];
		}
		return $records;
	}
	
	public function parse_where_statement($options){
		$check = array_keys($options);
		$this->depth++;
		if(!empty($check) && is_numeric($check[0])){
			foreach($options as $option){
				$this->parse_where_statement($option);
			}
		}else{
			$this->sub_query_parse($options);
		}
		$this->depth--;
	}
	
	public function sub_query_parse($options){
		$query_mode = 'AND';
		$backup = $this->where;
		$this->where = [];
		if(!empty($options['query_mode'])){
			$query_mode = $options['query_mode'];
		}
		$this->parse_select_options($options);
		if(!empty($options['sub_query'])){
			$this->parse_where_statement($options['sub_query']);
		}
		if(count($this->where) > 0){
			$where = '( '.implode(' '.$query_mode.' ', $this->where). ' )';
			$this->where = $backup;
			$this->where[] = $where;
		}else{
			$this->where = $backup;
		}
	}
	
	public function removeRecord($id){
		$sql = 'DELETE FROM `'.$this->table.'` WHERE `'.$this->primary_key.'` = :id';
		$this->params = [
			':id' => $id
		];
		$this->sql = $sql;
		$this->db->t_query($sql, $this->params);
	}
	
	public function buildParam($option, $field, $friendly = false, $compare = '='){
		if($friendly == false){
			$friendly = $field;
		}
		if(gettype($option) == 'array'){
			if(stristr($field, '.') === false){
				$field = $this->table . '.' . $field;
			}
			$temp_where = [];
			foreach($option as $key => $type){
				if($this->depth > 0){
					$key .= '__'.$this->depth;
				}
				if($type !== null){
					$temp_where [] = $field.' '.$compare.' :'.$friendly.'_'.$key;
					$this->params[':'.$friendly.'_'.$key] = $type;
				}else{
					$temp_where [] = $field.' IS NULL ';
				}
				
			}
			$temp_where = implode(' OR ', $temp_where);
			$temp_where = '( '.$temp_where .' )';
			$this->where[] = $temp_where;
		}else{
			if($this->depth > 0){
				$friendly .= '__'.$this->depth;
			}
			if($option !== null && $compare != 'IN' && $compare != 'NOT IN'){
				$this->where[] = '`'.$this->table.'`.`'.$field.'` '.$compare.' :'.$friendly;
				$this->params[':'.$friendly] = $option;
			}else if($option === null){
				if($compare == '='){
					$this->where[] = '`'.$this->table.'`.`'.$field.'` IS NULL';
				}else if($compare == '!='){
					$this->where[] = '`'.$this->table.'`.`'.$field.'` IS NOT NULL';
				}
			}else if($compare == 'IN'){
				$this->where[] = 'FIND_IN_SET(`'.$this->table.'`.`'.$field.'`, :'.$friendly.')';
				$this->params[':'.$friendly] = $option;
			}else if($compare == 'NOT IN'){
				$this->where[] = '!FIND_IN_SET(`'.$this->table.'`.`'.$field.'`, :'.$friendly.')';
				$this->params[':'.$friendly] = $option;
			}
		}
	}
	
	private function selfJoin($join_options){
		$index_field = $join_options['index_field'];
		$fields = $join_options['fields'];
		if(!is_array($fields)){
			$fields = [ $fields ];
		}
		$select = [];
		$joins = [];
		$i = 1;
		foreach($fields as $key => $f){
			if(is_numeric($key)){
				$key = $f;
				$f = 'parent_'.$f;
			}
			$select[] = 'p'.$i.'.`'.$key.'` AS `'.$f.'`';
			$joins[] = 'LEFT JOIN (SELECT * FROM `'.$this->table.'`) p'.$i.' on `'.$this->table.'`.`'.$index_field.'` = p'.$i.'.`'.$this->primary_key.'`';
			
			$this->structure[$f] = [
				'machine_name' => 'p'.$i.'.`'.$key.'`',
				'type' => 'text'
			];
			$i++;
		}
		return [
			'select' => $select,
			'joins' => $joins
		];
	}
	
	public function direct_link(db_handler $child, $options = false){
		load_class('direct_link_db_handler');
		if(class_exists('direct_link_db_handler')){
			if(empty($options)){
				$options = [];
			}
			$options['parent'] = $this;
			$options['child'] = $child;
			return new direct_link_db_handler($options);
		}else{
			return false;
		}
	}
	
	public function meta_link(db_handler $child, $options){
		if(empty($options['meta_key_field']) || empty($options['meta_value_field'])){
			return;
		}
		load_class('meta_link_db_handler');
		if(class_exists('meta_link_db_handler')){
			$options['parent'] = $this;
			$options['child'] = $child;
			return new meta_link_db_handler($options);
		}else{
			return false;
		}
	}

	public function get_sql($options){
		$options = array_merge($options, [
			'sql_only' => true
		]);
		return [
			'sql' => $this->getRecords($options),
			'params' => $this->params
		];
	}
}