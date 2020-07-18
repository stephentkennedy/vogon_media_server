<?php
switch($type){
	case 'head':
		$sql = 'SELECT in_h_nav FROM route WHERE route_id = :id';
		$params = [
			':id' => $id
		];
		$query = $db->query($sql, $params);
		$bool = $query->fetchAll()[0]['in_h_nav'];
		if($bool){
			$sql = 'UPDATE route SET in_h_nav = 0 WHERE route_id = :id';
		}else{
			$sql = 'UPDATE route SET in_h_nav = 1 WHERE route_id = :id';
		}
		$db->query($sql, $params);
		
		$sql = 'SELECT * FROM route WHERE in_h_nav = 1';
		$query = $db->query($sql);
		$nav_routes = $query->fetchAll();
		$array = [];
		$string = '';
		foreach($nav_routes as $r){
			$string .= '<a href="'.URI.'/'.$r['route_slug'].'">';
			if(empty($r['nav_display'])){
				$string .= $r['route_ext'];
			}else{
				$string .= $r['nav_display'];
			}
			$string .= '</a> ';
			$array[$r['route_id']] = $string;
			$string = '';
		}
		$string = json_encode($array);
		$sql = 'UPDATE var SET var_content = :content WHERE var_name = "header_menu"';
		$params = [
			':content' => $string
		];
		$db->query($sql, $params);
		break;
	case 'foot':
		$sql = 'SELECT in_f_nav FROM route WHERE route_id = :id';
		$params = [
			':id' => $id
		];
		$query = $db->query($sql, $params);
		$bool = $query->fetchAll()[0]['in_f_nav'];
		if($bool){
			$sql = 'UPDATE route SET in_f_nav = 0 WHERE route_id = :id';
		}else{
			$sql = 'UPDATE route SET in_f_nav = 1 WHERE route_id = :id';
		}
		$db->query($sql, $params);
		
		$sql = 'SELECT * FROM route WHERE in_f_nav = 1';
		$query = $db->query($sql);
		$nav_routes = $query->fetchAll();
		$array = [];
		$string = '';
		foreach($nav_routes as $r){
			$string .= '<a href="'.URI.'/'.$r['route_slug'].'">';
			if(empty($r['nav_display'])){
				$string .= $r['route_ext'];
			}else{
				$string .= $r['nav_display'];
			}
			$string .= '</a> ';
			$array[$r['route_id']] = $string;
			$string = '';
		}
		$string = json_encode($array);
		$sql = 'UPDATE var SET var_content = :content WHERE var_name = "footer_menu"';
		$params = [
			':content' => $string
		];
		$db->query($sql, $params);
		break;
}
?>