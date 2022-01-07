<?php
//This is a data table handling class for Vogon. Its goal is to automate the data-table and data-meta operations for the vogon framework
class clerk {
	private $params, $where, $sql;
	private $debug = false;
	public $db, $total_count; 
	public $record = []; //Currently unused
	
	public function __construct($db = false){
		if($db == false){
			//By allowing developers to provide their own db object, we should allow the easy connection to multiple databases. Allowing us to create several clerks for several copies of databases.
			global $db;
		}
		if(!empty($db)){
			$this->db = $db;
		}else{
			die('Clerk instantiated without database object in $db variable.');
		}
	}
	
	public function updateRecord($values, $id = false){
		if($id == false){
			return addRecord($values);
		}
		if(empty($values) || gettype($values) != 'array'){
			return false;
		}
		$sql = 'UPDATE `data` SET ';
		$this->where = [];
		$this->params = [];
		if(isset($values['name'])){
			$this->buildParam($values['name'], 'data_name', 'name');
		}
		if(isset($values['slug'])){
			$this->buildParam($values['slug'], 'data_slug', 'slug');
		}
		if(isset($values['content'])){
			$this->buildParam($values['content'], 'data_content', 'content');
		}
		if(isset($values['type'])){
			$this->buildParam($values['type'], 'data_type', 'type');
		}
		if(isset($values['parent'])){
			$this->buildParam($values['parent'], 'data_parent', 'parent');
		}
		if(isset($values['status'])){
			$this->buildParam($values['status'], 'data_status', 'status');
		}
		if(isset($values['user'])){
			$this->buildParam($values['user'], 'user_key', 'user');
		}
		$set = implode(', ', $this->where);
		$sql .= $set . ' WHERE data_id = :id';
		$this->params[':id'] = $id;
		return $this->db->query($sql, $this->params);
	}
	
	public function addRecord($values, $metas = false){
		$sql = 'INSERT INTO `data` (`data_name`, `data_slug`, `data_content`, `data_type`, `data_parent`, `data_status`, `user_key`) VALUES (:name, :slug, :content, :type, :parent, :status, :user)';
		$this->params = [
			':name' => '',
			':slug' => '',
			':content' => '',
			':type' => '',
			':parent' => 0,
			':status' => '',
			':user' => 0
		];
		if(isset($values['name'])){
			$this->params[':name'] = $values['name'];
		}
		if(isset($values['slug'])){
			$this->params[':slug'] = $values['slug'];
		}
		if(isset($values['content'])){
			$this->params[':content'] = $values['content'];
		}
		if(isset($values['type'])){
			$this->params[':type'] = $values['type'];
		}
		if(isset($values['parent'])){
			$this->params[':parent'] = $values['parent'];
		}
		if(isset($values['status'])){
			$this->params[':status'] = $values['status'];
		}
		if(isset($values['user'])){
			$this->params[':user'] = $values['user'];
		}
		$this->sql = $sql;
		$this->db->query($sql, $this->params);
		if($metas === false){
			return $this->db->last;
		}else{
			$record_id = $this->db->last;
			$this->addMetas($record_id, $metas);
			return $record_id;
		}
	}
	
	public function addMetas($record_id, $values){
		$sql = 'INSERT INTO `data_meta` (data_id, data_meta_name, data_meta_content) VALUES (:id, :name, :content)';
		foreach($values as $name => $content){
			$params = [
				':id' => $record_id,
				':name' => $name,
				':content' => $content
			];
			$query = $this->db->query($sql, $params);
			if($query == false && $this->debug == true){
				debug_d($this->db->error);
			}
		}
	}
	
	public function updateMetas($record_id, $values){
		$check_sql = 'SELECT * FROM `data_meta` WHERE `data_meta_name` = :name AND `data_id` = :id';
		$sql = 'UPDATE `data_meta` SET data_meta_content = :content WHERE data_meta_name = :name AND data_id = :id';
		foreach($values as $name => $content){
			$params = [
				':id' => $record_id,
				':name' => $name,
				':content' => $content
			];
			$check_params = [
				':id' => $record_id,
				':name' => $name,
			];
			$query = $this->db->query($check_sql, $check_params);
			if(!empty($query)){
				$check = $query->fetch();
				if($check == false){
					$tsql = 'INSERT INTO `data_meta` (data_id, data_meta_name, data_meta_content) VALUES (:id, :name, :content)';
					$this->db->query($tsql, $params);
				}else{
					$this->db->query($sql, $params);
				}
			}else{
				debug_d($this->db->error);
			}
		}
	}
	
	public function removeMetas($record_id, $values){
		$sql = 'DELETE FROM `data_meta` WHERE data_id = :id AND data_meta_name = :name';
		foreach($values as $name){
			$params = [
				':id' => $record_id,
				':name' => $name
			];
			$this->db->query($sql, $this->params);
		}
	}
	
	public function getRecords($options, $meta = false){
		if(is_numeric($options)){
			return $this->getRecord(['id' => $options]);
		}
		if(isset($options['metas'])){
			return $this->metaComplex($options);
		}
		if(isset($options['limit'])){
			$cql = 'SELECT COUNT(*) as `count` FROM `data`';
		}
		$sql = 'SELECT * FROM `data`';
		$this->where = [];
		$this->params = [];
		if(isset($options['type'])){
			$this->buildParam($options['type'], 'data_type', 'type');
		}
		if(isset($options['search_type'])){
			$this->buildParam($options['type'], 'data_type', 'type', 'LIKE');
		}
		if(isset($options['user'])){
			$this->buildParam($options['user'], 'user_key', 'user');
		}
		if(isset($options['user_key'])){
			$this->buildParam($options['user_key'], 'user_key', 'user');
		}
		if(isset($options['status'])){
			$this->buildParam($options['status'], 'data_status', 'status');
		}
		if(isset($options['name'])){
			$this->buildParam($options['name'], 'data_name', 'name');
		}
		if(isset($options['search_name'])){
			$this->buildParam($options['name'], 'data_name', 'name', 'LIKE');
		}
		if(isset($options['parent'])){
			$this->buildParam($options['parent'], 'data_parent', 'parent');
		}
		if(isset($options['slug'])){
			$this->buildParam($options['slug'], 'data_slug', 'slug');
		}
		if(isset($options['search_slug'])){
			$this->buildParam($options['slug'], 'data_slug', 'slug', 'LIKE');
		}
		if(isset($options['id'])){
			$this->buildParam($options['id'], 'data_id', 'id');
		}
		if(isset($options['content'])){
			$this->buildParam($options['content'], 'data_content', 'content', 'LIKE');
		}
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
		if(isset($options['orderby'])){
			$sql .= ' ORDER BY '.$options['orderby'];
		}
		if(isset($options['limit'])){
			$sql .= ' LIMIT '.$options['limit'];
		}
		$this->sql = $sql;
		$query = $this->db->query($sql, $this->params);
		if($query != false){
			$records = $query->fetchAll();
		}else{
			$records = [];
		}
		if($meta != false){
			foreach($records as $key => $r){
				$records[$key]['meta'] = $this->getMetas($r['data_id']);
			}
		}
		return $records;
	}
	
	public function getRecord($options, $meta = false){
		$sql = 'SELECT * FROM `data`';
		$this->where = [];
		$this->params = [];
		if(is_numeric($options)){
			$options = [
				'id' => $options
			];
		}
		if(isset($options['type'])){
			$this->buildParam($options['type'], 'data_type', 'type');
		}
		if(isset($options['search_type'])){
			$this->buildParam($options['type'], 'data_type', 'type', 'LIKE');
		}
		if(isset($options['user'])){
			$this->buildParam($options['user'], 'user_key', 'user');
		}
		if(isset($options['user_key'])){
			$this->buildParam($options['user_key'], 'user_key', 'user');
		}
		if(isset($options['status'])){
			$this->buildParam($options['status'], 'data_status', 'status');
		}
		if(isset($options['name'])){
			$this->buildParam($options['name'], 'data_name', 'name');
		}
		if(isset($options['search_name'])){
			$this->buildParam($options['name'], 'data_name', 'name', 'LIKE');
		}
		if(isset($options['parent'])){
			$this->buildParam($options['parent'], 'data_parent', 'parent');
		}
		if(isset($options['slug'])){
			$this->buildParam($options['slug'], 'data_slug', 'slug');
		}
		if(isset($options['search_slug'])){
			$this->buildParam($options['slug'], 'data_slug', 'slug', 'LIKE');
		}
		if(isset($options['id'])){
			$this->buildParam($options['id'], 'data_id', 'id');
		}
		if(isset($options['content'])){
			$this->buildParam($options['content'], 'data_content', 'content', 'LIKE');
		}
		$sql .= ' WHERE ' . implode(' AND ', $this->where);
		if(isset($options['orderby'])){
			$sql .= ' ORDER BY '.$options['orderby'];
		}
		$this->sql = $sql;
		
		$query = $this->db->query($sql, $this->params);
		if($query != false){
			$records = $query->fetch();
		}else{
			$records = [];
		}
		if($meta != false){
			$records['meta'] = $this->getMetas($records['data_id']);
		}
		return $records;
	}
	
	public function getMetas($id, $options = false){
		$sql = 'SELECT * FROM data_meta WHERE data_id = :id';
		$this->params = [
			':id' => $id
		];
		if($options != false){
			$this->where = [];
			if(isset($options['name'])){
				$this->buildParam($options['name'], 'data_meta_name', 'name');
			}
			if(isset($options['search_name'])){
				$this->buildParam($options['name'], 'data_meta_name', 'name', 'LIKE');
			}
			if(isset($options['content'])){
				$this->buildParam($options['name'], 'data_meta_content', 'content', 'LIKE');
			}
			$where = implode(' AND ', $this->where);
			$sql .= ' '.$where;
		}
		$metas = [];
		$this->sql = $sql;
		$query = $this->db->query($sql, $this->params);
		if($query != false){
			$records = $query->fetchAll();
		}else{
			$records = [];
		}
		foreach($records as $r){
			$metas[$r['data_meta_name']] = $r['data_meta_content'];
		}
		return $metas;
	}
	
	public function removeRecord($id){
		$sql = 'DELETE FROM data_meta WHERE data_id = :id';
		$this->params = [
			':id' => $id
		];
		$this->sql = $sql;
		$this->db->query($sql, $this->params);
		$sql = 'DELETE FROM data WHERE data_id = :id';
		$this->params = [
			':id' => $id
		];
		$this->sql = $sql;
		$this->db->query($sql, $this->params);
	}
	
	private function metaComplex($options){
		$sql = 'SELECT `data`.*, ';
		$mj = $this->metaJoin($options['metas']);
		$sql .= implode(', ', $mj['select']) . ' FROM `data` ' . implode(' ', $mj['joins']);
		
		$where = [];
		$params = [];
		
		/*
		Name: Stephen Kennedy
		Date: 8/5/2020
		Comment: Since we're joining here, we're only allowing a subset of the options we offer in the main function.
		*/
		
		if(isset($options['type'])){
			$where[] = '`data`.`data_type` = :type';
			$params[':type'] = $options['type'];
		}
		if(isset($options['id'])){
			$where[] = '`data`.`data_id` = :id';
			$params[':id'] = $options['id'];
		}
		if(isset($options['parent'])){
			$where[] = '`data`.`data_parent` = :parent';
			$params[':parent'] = $options['parent'];
		}
		if(isset($options['search_name'])){
			$where[] ='`data`.`data_name` LIKE :search_name';
			$params[':search_name'] = '%'.$options['search_name'].'%';
		}
		if(isset($options['search_content']) || isset($options['content'])){
			$where[] ='`data`.`data_content` LIKE :search_content';
			$params[':search_content'] ='%'.$options['search_content'].'%';
		}
		if(isset($options['search_meta']) && !is_array($options['search_meta'])){
			$sub_where = [];
			if(isset($options['search_meta_mode'])){
				$mode = $options['search_meta_mode'];
			}else{
				$mode = 'like';
			}
			switch($mode){
				default:
				case 'like':
					$params[':search_meta'] = '%'.$options['search_meta'].'%';
					foreach($options['metas'] as $key => $m){
						$sub_where[] = 'm'.$key.'.data_meta_content LIKE :search_meta';
					}
					break;
				case 'strict':
					$params[':search_meta'] = $options['search_meta'];
					foreach($options['metas'] as $key => $m){
						$sub_where[] = 'm'.$key.'.data_meta_content = :search_meta';
					}
					break;
				case 'is not':
					//$params[':search_meta'] = $options['search_meta'];
					foreach($options['metas'] as $key => $m){
						$sub_where[] = 'm'.$key.'.data_meta_content IS NOT NULL';
					}
					break;
				case 'is':
					foreach($options['metas'] as $key => $m){
						$sub_where[] = 'm'.$key.'.data_meta_content IS NULL';
					}
					break;
			}
			$where[] = '('.implode(' OR ', $sub_where).')';
		}else if(isset($options['search_meta']) && is_array($options['search_meta'])){
			if(isset($options['search_meta_mode'])){
				$mode = $options['search_meta_mode'];
			}else{
				$mode = 'like';
			}
			foreach($options['search_meta'] as $field => $search){
				switch($mode){
					default:
					case 'like':
						$key = array_search($field, $options['metas']);
						$where[] = 'm'.$key.'.data_meta_content LIKE :search_meta'.$key;
						$params[':search_meta'.$key] = '%'.$search.'%';
						break;
					case 'strict':
						$key = array_search($field, $options['metas']);
						$where[] = 'm'.$key.'.data_meta_content = :search_meta'.$key;
						$params[':search_meta'.$key] = $search;
						break;
					case 'is not':
						$key = array_search($field, $options['metas']);
						$where[] = 'm'.$key.'.data_meta_content IS NOT NULL';
						break;
					case 'is':
						$key = array_search($field, $options['metas']);
						$where[] = 'm'.$key.'.data_meta_content IS NULL';
						break;
				}
			}
		}
		
		$sql .= ' WHERE ' . implode(' AND ', $where);
		if(isset($options['orderby'])){
			$sql .= ' ORDER BY '.$options['orderby'];
		}
		
		$query = $this->db->query($sql, $params);
		if($query != false){
			$records = $query->fetchAll();
		}else{
			$records = [];
		}
		return $records;
	}
	
	private function buildParam($option, $field, $friendly = false, $compare = '='){
		if($friendly == false){
			$friendly = $field;
		}
		if(gettype($option) == 'array'){
			$temp_where = [];
			foreach($options['type'] as $key => $type){
				$temp_where [] = '`'.$field.'` '.$compare.' :'.$friendly.'_'.$key;
				$this->params[':'.$friendly.'_'.$key] = $type;
			}
			$temp_where = implode(' OR ', $temp_where);
			$temp_where = '( '.$temp_where .' )';
			$this->where[] = $temp_where;
		}else{
			$this->where[] = '`'.$field.'` '.$compare.' :'.$friendly;
			$this->params[':'.$friendly] = $option;
		}
	}
	
	private function metaJoin($fields){
		$select = [];
		$joins = [];
		if(!is_array($fields)){
			$fields = [$fields];
		}
		foreach($fields as $key => $f){
			$select[] = 'm'.$key.'.data_meta_content AS `'.$f.'`';
			$joins[] = 'LEFT JOIN (SELECT * FROM data_meta WHERE data_meta_name = "'.$f.'") m'.$key.' on `data`.`data_id` = m'.$key.'.data_id';
		}
		return [
			'select' => $select,
			'joins' => $joins
		];
	}
}