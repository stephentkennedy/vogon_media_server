<?php
switch($type){
	case 'head':
		$sql = 'SELECT in_h_nav FROM route WHERE route_id = :id';
		$params = [
			':id' => $id
		];
		$query = $db->t_query($sql, $params);
		$bool = $query->fetchAll()[0]['in_h_nav'];
		if($bool){
			$sql = 'UPDATE route SET in_h_nav = 0 WHERE route_id = :id';
		}else{
			$sql = 'UPDATE route SET in_h_nav = 1 WHERE route_id = :id';
		}
		$db->t_query($sql, $params);
		
		break;
	case 'foot':
		$sql = 'SELECT in_f_nav FROM route WHERE route_id = :id';
		$params = [
			':id' => $id
		];
		$query = $db->t_query($sql, $params);
		$bool = $query->fetchAll()[0]['in_f_nav'];
		if($bool){
			$sql = 'UPDATE route SET in_f_nav = 0 WHERE route_id = :id';
		}else{
			$sql = 'UPDATE route SET in_f_nav = 1 WHERE route_id = :id';
		}
		$db->t_query($sql, $params);
		
		break;
}
load_model('rebuild_nav', ['type' => $type]);
?>