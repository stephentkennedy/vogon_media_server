<?php

class meta_link_db_handler extends db_handler{
	public $root;
	public $child;
	public $backup_structure;
	public $link_field = false;
	public $meta_key_field;
	public $meta_value_field;
	
	public function __construct($options){
		if(empty($options['parent']) || empty($options['child']) || empty($options['meta_key_field']) || empty($options['meta_value_field'])){
			return false;
		}
		if(!empty($options['link_field'])){
			$this->link_field = $options['link_field'];
		}
		$this->root = $options['parent'];
		$this->child = $options['child'];
		$this->meta_key_field = $options['meta_key_field'];
		$this->meta_value_field = $options['meta_value_field'];
		$this->db = $this->root->db;
		return $this->init();
	}
	
	
	private function init(){
		$pri = $this->root->primary_key;
		if(!empty($this->link_field)){
			$pri = $this->link_field;
		}else{
			$this->link_field = $pri;
		}
		if(empty($this->child->structure[$pri])){
			return false;
		}
		$this->structure = [];
		foreach($this->root->structure as $key => $value){
			$value['machine_name'] = 
			'`'.$this->root->table.'`.`'.$value['machine_name'].'`';
			$this->structure[$key] = $value;
		}
		$this->backup_structure = $this->structure;
		$this->primary_key = $this->root->primary_key;
		return true;
	}
	
	public function getRecords($options){
		if(is_numeric($options)){
			return $this->getRecord(['id' => $options]);
		}
		if(empty($options['meta'])){
			return $this->root->getRecords($options);
		}
		$sql = 'SELECT `'.$this->root->table.'`.*, ';
		$mj = $this->metaJoin($options['meta']);
		if(!empty($options['self_join'])){
			$sj = $this->selfJoin($options['self_join']);
			if(!empty($sj)){
				foreach($sj['select'] as $select){
					$mj['select'][] = $select;
				}
				foreach($sj['joins'] as $join){
					$mj['joins'][] = $join;
				}
			}
			unset($options['self_join']);
		}
		if(isset($options['groupby']) && !empty($this->structure[$options['groupby']]['machine_name'])){
			$sql .= 'COUNT('.$this->structure[$options['groupby']]['machine_name'].') as "count", ';
		}
		unset($options['meta']);
		$sql .= implode(', ', $mj['select']) . ' FROM `'.$this->root->table.'` '. implode(' ', $mj['joins']);
		if(isset($options['limit'])){
			$cql = 'SELECT COUNT(*) as `count` FROM `'.$this->root->table.'` '. implode(' ', $mj['joins']);
		}
		$this->query_mode = 'AND';
		if(!empty($options['query_mode'])){
			$this->query_mode = $options['query_mode'];
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
			$where = implode(' '. $this->query_mode .' ', $this->where);
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
		if(isset($options['orderby']) 
		&& is_string($options['orderby']) 
		&& !empty($this->structure[$options['orderby']])){
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
		}else if (
			isset($options['orderby']) 
			&& is_array($options['orderby'])
		){
			$order_by = [];
			foreach($options['orderby'] as $column){
				if(!empty($this->structure[rtrim($column, ' + 0')])){
					$clean_column = rtrim($column, ' + 0');
					if($clean_column == $column){
						$order_by[] = $this->structure[$clean_column]['machine_name'];
					}else{
						$order_by[] = $this->structure[$clean_column]['machine_name'].' + 0';
					}

				}
			}
			if(!empty($order_by)){
				$sql .= ' ORDER BY '.implode(', ', $order_by);
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
		if(isset($options['limit']) && is_numeric($options['limit'])){
			$sql .= ' LIMIT ';
			if(isset($options['offset']) && is_numeric($options['offset'])){
				$sql .= $options['offset'].',';
			}
			$sql .= $options['limit'];
		}
		$this->sql = $sql;
		$query = $this->db->query($sql, $this->params);
		if($query != false){
			$records = $query->fetchAll();
		}else{
			$records = [];
		}
		return $records;
	}
	
	public function getRecord($options){
		if(empty($options['meta'])){
			return $this->root->getRecord($options);
		}
		$sql = 'SELECT `'.$this->root->table.'`.*, ';
		$mj = $this->metaJoin($options['meta']);
		if(!empty($options['self_join'])){
			$sj = $this->selfJoin($options['self_join']);
			if(!empty($sj)){
				foreach($sj['select'] as $select){
					$mj['select'][] = $select;
				}
				foreach($sj['joins'] as $join){
					$mj['joins'][] = $join;
				}
			}
			unset($options['self_join']);
		}
		unset($options['meta']);
		$sql .= implode(', ', $mj['select']). 'FROM `'.$this->root->table.'` '.implode(' ', $mj['joins']);
		$this->where = [];
		$this->params = [];
		$this->depth = 0;
		if(is_numeric($options)){
			$options = [
				'id' => $options
			];
		}
		$this->query_mode = 'AND';
		if(!empty($options['query_mode'])){
			$this->query_mode = $options['query_mode'];
			unset($options['query_mode']);
		}
		$this->parse_select_options($options);
		if(isset($options['sub_query'])){
			$this->parse_where_statement($options['sub_query']);
		}
		$sql .= ' WHERE ' . implode(' '. $this->query_mode .' ', $this->where);
		
		if(isset($options['orderby']) 
		&& is_string($options['orderby']) 
		&& !empty($this->structure[$options['orderby']])){
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
		}else if (
			isset($options['orderby']) 
			&& is_array($options['orderby'])
		){
			$order_by = [];
			foreach($options['orderby'] as $column){
				if(!empty($this->structure[rtrim($column, ' + 0')])){
					$order_by[] = $column;
				}
			}
			if(!empty($order_by)){
				$sql .= ' ORDER BY '.implode(', ', $order_by);
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
		$query = $this->db->query($sql, $this->params);
		if($query != false){
			$records = $query->fetch();
		}else{
			$records = [];
		}
		return $records;
	}
	
	public function removeRecord($id){
		$sql = 'DELETE FROM `'.$this->child->table.'` WHERE `'.$this->root->primary_key.'` = :id';
		$this->params = [
			':id' => $id
		];
		$this->sql = $sql;
		$this->db->query($sql, $this->params);
		$this->root->removeRecord($id);
	}
	
	public function addRecord($values){
		$parent_id = $this->root->addRecord($values);
		$meta_values = $this->filter_values($values, $parent_id);
		
		$this->child->addRecord($meta_values);
		return $parent_id;
	}
	
	public function updateRecord($values, $record_id = false){
		return false;
		if($record_id == false){
			return $this->addRecord($values);
		}
		$this->root->updateRecord($values);
		
	}
	
	private function filter_values($values, $parent_id = false){
		$meta_values = [];
		if($parent_id != false){
			$meta_values = [
				$this->root->primary_key => $parent_id
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
	private function metaJoin($fields){
		$this->structure = $this->backup_structure;
		$select = [];
		$joins = [];
		if(!is_array($fields)){
			$fields = [$fields];
		}
		$i = 1;
		foreach($fields as $key => $f){
			if(is_numeric($key)){
				$key = $f;
				$f = 'meta_'.$f;
			}
			$select[] = 'm'.$i.'.`'.$this->meta_value_field.'` AS `'.$key.'`';
			$joins[] = 'LEFT JOIN (SELECT * FROM `'.$this->child->table.'` WHERE `'.$this->meta_key_field.'` = "'.$key.'") m'.$i.' on `'.$this->root->table.'`.`'.$this->primary_key.'` = m'.$i.'.`'.$this->link_field.'`';
			
			$this->structure[$f] = [
				'machine_name' => 'm'.$i.'.`'.$this->meta_value_field.'`',
				'type' => 'text'
			];
			$i++;
		}
		return [
			'select' => $select,
			'joins' => $joins
		];
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
			$joins[] = 'LEFT JOIN (SELECT * FROM `'.$this->root->table.'`) p'.$i.' on `'.$this->root->table.'`.`'.$index_field.'` = p'.$i.'.`'.$this->root->primary_key.'`';
			
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
		load_class('meta_to_direct_link_db_handler');
		if(class_exists('meta_to_direct_link_db_handler')){
			if(empty($options)){
				$options = [];
			}
			$options['parent'] = $this;
			$options['child'] = $child;
			return new meta_to_direct_link_db_handler($options);
		}else{
			return false;
		}
	}
}