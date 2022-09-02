<?php
switch($type){
	case 'head':
		
		$sql = 'SELECT * FROM route WHERE in_h_nav = 1 ORDER BY route_ord, nav_display ASC';
		$query = $db->t_query($sql);
		$nav_routes = $query->fetchAll();
		$array = [];
		$string = '';
		foreach($nav_routes as $r){
			$string .= '<a class="nav '.$r['route_ext'].'" href="'.build_slug($r['route_slug']).'">';
			if(empty($r['nav_display'])){
				$string .= $r['route_ext'];
			}else{
				$string .= $r['nav_display'];
			}
			$string .= '</a> ';
			if($r['ext_primary'] == 1){
				$subnav = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $r['route_ext'] . DIRECTORY_SEPARATOR . 'subnav.json';
				if(file_exists($subnav)){
					$subnav = file_get_contents($subnav);
					$subnav = json_decode($subnav, true);
					foreach($subnav as $nav){
						$string .= '<a class="subnav '.$r['route_ext'].'" ';
						if(!empty($nav['link'])){
							$string .= 'href="'.build_slug($nav['link'], [], $r['route_ext']).'" ';
						}
						if(!empty($nav['data'])){
							foreach($nav['data'] as $name => $value){
								$string .= 'data-'.$name.'="'.$value.'" ';
							}
						}
						
						$string .= '>'.$nav['display'].'</a> ';
					}
				}
			}
			$array[$r['route_id']] = $string;
			$string = '';
		}
		$string = json_encode($array);
		$sql = 'UPDATE var SET var_content = :content WHERE var_name = "header_menu"';
		$params = [
			':content' => $string
		];
		$db->t_query($sql, $params);
		break;
	case 'foot':
		
		$sql = 'SELECT * FROM route WHERE in_f_nav = 1 ORDER BY route_ord, nav_display ASC';
		$query = $db->t_query($sql);
		$nav_routes = $query->fetchAll();
		$array = [];
		$string = '';
		foreach($nav_routes as $r){
			$string .= '<a href="'.build_slug($r['route_slug']).'">';
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
		$db->t_query($sql, $params);
		break;
}
