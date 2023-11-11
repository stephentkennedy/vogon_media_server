<?php
/*
Name: Steph Kennedy
Date: 10/22/21
Comment: At this point we are joining a meta join with a direct join, we're just not going to support any transactions except selects to make our lives easier.
*/
class meta_to_direct_link_db_handler extends meta_link_db_handler {
	public $root;
	public $child;
	public $linked;
	public $primary_friendly;
	public $parent_join_key = false;
	public $child_primary_key = false;
	
	public function __construct($options){
		if(empty($options['parent']) || empty($options['child'])){
			return false;
		}
		$this->root = $options['parent']->root;
		$this->child = $options['parent']->child;
		$this->linked = $options['child'];
		$this->link_field = $options['parent']->link_field;
		$this->meta_key_field = $options['parent']->meta_key_field;
		$this->meta_value_field = $options['parent']->meta_value_field;
		$this->db = $this->root->db;
		if(!empty($options['join_key'])){
			$this->parent_join_key = $options['join_key'];
		}
		if(!empty($options['linked_join_key'])){
			$this->linked_join_key = $options['linked_join_key'];
		}
		$this->init();
	}
	
	private function init(){
		if(empty($this->parent_join_key)){
			$pri = $this->root->primary_key;
			$this->parent_join_key = $pri;
		}else{
			$pri = $this->parent_join_key;
		}
		if(empty($this->linked_join_key)){
			$this->linked_join_key = $this->parent_join_key;
		}
		$this->structure = $this->root->structure;
		foreach($this->root->structure as $key => $value){
			$value['machine_name'] = 
			'`'.$this->root->table.'`.`'.$value['machine_name'].'`';
			$this->structure[$key] = $value;
		}
		foreach($this->linked->structure as $key => $value){
			if($value['machine_name'] == $pri){
				continue;
			}
			$safe_key = 'link_'.$key;
			if(!empty($this->structure[$safe_key])){
				$safe_key = 'link_'.$safe_key;
			}
			$value['machine_name'] = 
			'`'.$this->linked->table.'`.`'.$value['machine_name'].'`';
			$this->structure[$safe_key] = $value;
		}
		return true;
	}
	
	public function getRecord($options){
		$this->backup_structure = $this->structure;
		$sql = 'SELECT `'.$this->root->table.'`.*, `'.$this->linked->table.'`.*, ';
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
		$sql .= implode(', ', $mj['select']). 'FROM `'.$this->root->table.'` '.implode(' ', $mj['joins']).', `'.$this->linked->table.'`';
		$this->where = [];
		$this->params = [];
		$this->depth = 0;
		if(is_numeric($options)){
			$options = [
				'id' => $options
			];
		}
		$this->t_query_mode = 'AND';
		if(!empty($options['query_mode'])){
			$this->t_query_mode = $options['query_mode'];
			unset($options['query_mode']);
		}
		unset($options['meta']);
		$this->parse_select_options($options);
		if(isset($options['sub_query'])){
			$this->parse_where_statement($options['sub_query']);
		}
		$this->where[] = '`'.$this->root->table.'`.`'.$this->parent_join_key.'` = `'.$this->linked->table.'`.`'.$this->linked_join_key.'`';
		$sql .= ' WHERE ' . implode(' '. $this->t_query_mode .' ', $this->where);
		
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
		$query = $this->db->t_query($sql, $this->params);
		if($query != false){
			$records = $query->fetch();
		}else{
			$records = [];
		}
		return $records;
	}
	
	public function getRecords($options){
		$this->backup_structure = $this->structure;
		if(is_numeric($options)){
			return $this->getRecord(['id' => $options]);
		}
		$sql = 'SELECT `'.$this->root->table.'`.*, `'.$this->linked->table.'`.*, ';
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
		$sql .= implode(', ', $mj['select']) . ' FROM `'.$this->root->table.'` '. implode(' ', $mj['joins']).', `'.$this->linked->table.'`';
		if(isset($options['limit'])){
			$cql = 'SELECT COUNT(*) as `count` FROM `'.$this->root->table.'` '. implode(' ', $mj['joins']).', `'.$this->linked->table.'`';
		}
		$this->t_query_mode = 'AND';
		if(!empty($options['query_mode'])){
			$this->t_query_mode = $options['query_mode'];
			unset($options['query_mode']);
		}
		$this->where = [];
		$this->params = [];
		$this->depth = 0;
		unset($options['meta']);
		$this->parse_select_options($options);
		if(isset($options['sub_query'])){
			$this->parse_where_statement($options['sub_query']);
		}
		$this->where[] = '`'.$this->root->table.'`.`'.$this->parent_join_key.'` = `'.$this->linked->table.'`.`'.$this->linked_join_key.'`';
		if(count($this->where) > 0){
			$where = implode(' '. $this->t_query_mode .' ', $this->where);
			$sql .= ' WHERE '.$where;
			if(isset($cql)){
				$cql .= ' WHERE '.$where;
			}
		}
		if(isset($cql)){
			$count = $this->db->t_query($cql, $this->params)->fetch()['count'];
			$this->total_count = $count;
		}
		if(isset($options['groupby']) && !empty($this->structure[$options['groupby']])){
			$sql .= ' GROUP BY '.$this->structure[$options['groupby']]['machine_name'];
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
	
	//Inherited but may need to be modified
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
		debug_d($meta_values);
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
				$temp_where [] = ''.$field.' '.$compare.' :'.$friendly.'_'.$key;
				$this->params[':'.$friendly.'_'.$key] = $type;
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
			$joins[] = 'LEFT JOIN (SELECT * FROM `'.$this->child->table.'` WHERE `'.$this->meta_key_field.'` = "'.$key.'") m'.$i.' on `'.$this->root->table.'`.`'.$this->root->primary_key.'` = m'.$i.'.`'.$this->link_field.'`';
			
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
}