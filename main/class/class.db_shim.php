<?php 
class db_shim extends thumb{
	public function query($sql, $params = []){
		return $this->t_query($sql, $params);
	}
}
