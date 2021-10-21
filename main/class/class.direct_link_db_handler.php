<?php

class direct_link_db_handler extends db_handler{
	public $root;
	public $child;
	public $primary_friendly;
	public $parent_join_key = false;
	public $child_primary_key = false;
	
	public function __construct($options){
		if(empty($options['parent']) || empty($options['child'])){
			return false;
		}
		$this->root = $options['parent'];
		$this->child = $options['child'];
		$this->db = $this->root->db;
		if(!empty($options['join_key'])){
			$this->parent_join_key = $options['join_key'];
		}
		if(!empty($options['child_join_key'])){
			$this->child_join_key = $options['child_join_key'];
		}
		$this->init();
	}
	
	
	private function init(){
		if(empty($this->parent_join_key)){
			$pri = $this->root->primary_key;
		}else{
			$pri = $this->parent_join_key;
		}
		if(empty($this->child_join_key)){
			$this->child_join_key = $this->parent_join_key;
		}
		$this->structure = $this->root->structure;
		foreach($this->root->structure as $key => $value){
			$value['machine_name'] = 
			'`'.$this->root->table.'`.`'.$value['machine_name'].'`';
			$this->structure[$key] = $value;
		}
		foreach($this->child->structure as $key => $value){
			if($value['machine_name'] == $pri){
				continue;
			}
			$safe_key = 'meta_'.$key;
			if(!empty($this->structure[$safe_key])){
				$safe_key = 'meta_'.$safe_key;
			}
			$value['machine_name'] = 
			'`'.$this->child->table.'`.`'.$value['machine_name'].'`';
			$this->structure[$safe_key] = $value;
		}
		return true;
	}
	
	public function getRecords($options){
		if(isset($options['limit'])){
			$cql = 'SELECT COUNT(*) as `count` FROM `'.$this->root->table.'`, `'.$this->child->table.'`';
		}
		$sql = 'SELECT * FROM `'.$this->root->table.'`, `'.$this->child->table.'`';
		$this->where = [];
		$this->params = [];
		$this->parse_select_options($options);
		$this->where[] = '`'.$this->root->table.'`.`'.$this->parent_join_key.'` = `'.$this->child->table.'`.`'.$this->child_join_key.'`';
		if(count($this->where) > 0){
			$where = implode(' AND ', $this->where);
			$sql .= ' WHERE '.$where;
			if(isset($cql)){
				$cql .= ' WHERE '.$where;
			}
		}
		if(isset($cql)){
			$count = $this->db->query($cql, $this->params)->fetch()['count'];
			$this->total_count = $count;
		}
		if(isset($options['groupby']) && !empty($this->structure[$options['groupby']])){
			$sql .= ' GROUP BY '.$this->structure[$options['groupby']]['machine_name'];
		}
		if(isset($options['orderby']) && !empty($this->structure[$options['orderby']])){
			$sql .= ' ORDER BY '.$this->structure[$options['orderby']]['machine_name'];
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
		if(isset($options['limit']) && (is_numeric($options['limit']) || is_numeric(str_replace([',', ' '], '', $options['limit'])))){
			$sql .= ' LIMIT '.$options['limit'];
		}
		$this->sql = $sql;
		$query = $this->db->query($sql, $this->params);
		if($query != false){
			$records = $query->fetchAll();
		}else{
			debug_d($this->db->error);
			$records = [];
		}
		return $records;
	}
	
	public function getRecord($options){
		$sql = 'SELECT * FROM `'.$this->root->table.'`, `'.$this->child->table.'`';
		$this->where = [];
		$this->params = [];
		$this->where[] = '`'.$this->root->table.'`.`'.$this->parent_join_key.'` = `'.$this->child->table.'`.`'.$this->child_join_key.'`';
		$this->parse_select_options($options);
		$sql .= ' WHERE ' . implode(' AND ', $this->where);
		
		if(isset($options['orderby']) && !empty($this->structure[$options['orderby']])){
			$sql .= ' ORDER BY '.$this->structure[$options['orderby']]['machine_name'];
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
		$query = $this->db->query($sql, $this->params);
		if($query != false){
			$records = $query->fetch();
		}else{
			debug_d($this->db->error);
			$records = [];
		}
		return $records;
	}
	
	public function removeRecord($id){
		$sql = 'DELETE FROM `'.$this->child->table.'` WHERE `'.$this->child_join_key.'` = :id';
		$this->params = [
			':id' => $id
		];
		$this->sql = $sql;
		$this->db->query($sql, $this->params);
		$this->root->removeRecord($id);
	}
	
	public function addRecord($values){
		$parent_id = $this->root->addRecord($values);
		$numeric_key = $parent_id;
		if($this->root->primary_key != $this->parent_join_key){
			$record = $this->root->getRecord($parent_id);
			$parent_id = $record[$this->parent_join_key];
		}
		$meta_values = $this->filter_values($values, $parent_id);
		
		
		$this->child->addRecord($meta_values);
		return $numeric_key;
	}
	
	public function updateRecord($values, $record_id = false){
		if($record_id == false){
			return $this->addRecord($values);
		}
		$check = $this->getRecord($record_id);
		$this->root->updateRecord($values, $record_id);
		if($check[$this->parent_join_key] != $record_id){
			$record_id = $check[$this->child->primary_key];
		}
		$meta_values = $this->filter_values($values, $record_id);
		$this->child->updateRecord($meta_values, $record_id);
	}
	
	private function filter_values($values, $parent_id = false){
		$meta_values = [];
		if($parent_id != false){
			$meta_values = [
				$this->child_join_key => $parent_id
			];
		}
		$leftovers = array_diff(array_keys($values), array_keys($this->root->structure));
		foreach($leftovers as $key){
			$safe_key = $key;
			if(substr($key, 0, 5) == 'meta_'){
				$safe_key = substr($key, 5);
			}
			$meta_values[$safe_key] = $values[$key];
		}
		return $meta_values;
	}
	
	public function buildParam($option, $field, $friendly = false, $compare = '='){
		if($friendly == false){
			$friendly = $field;
		}
		if(gettype($option) == 'array'){
			$temp_where = [];
			foreach($options['type'] as $key => $type){
				$temp_where [] = ''.$field.' '.$compare.' :'.$friendly.'_'.$key;
				$this->params[':'.$friendly.'_'.$key] = $type;
			}
			$temp_where = implode(' OR ', $temp_where);
			$temp_where = '( '.$temp_where .' )';
			$this->where[] = $temp_where;
		}else{
			if($option !== null && $compare != 'IN' && $compare != 'NOT IN'){
				$this->where[] = ''.$field.' '.$compare.' :'.$friendly;
				$this->params[':'.$friendly] = $option;
			}else if($option === null){
				if($compare == '='){
					$this->where[] = ''.$field.' IS NULL';
				}else if($compare == '!='){
					$this->where[] = ''.$field.' IS NOT NULL';
				}
			}else if($compare == 'IN'){
				$this->where[] = 'FIND_IN_SET('.$field.', :'.$friendly.')';
				$this->params[':'.$friendly] = $option;
			}else if($compare == 'NOT IN'){
				$this->where[] = '!FIND_IN_SET('.$field.', :'.$friendly.')';
				$this->params[':'.$friendly] = $option;
			}
		}
	}
}