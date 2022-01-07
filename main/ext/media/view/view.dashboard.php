<header><h1>Dashboard</h1></header>
<?php if(!empty($recent)){
	echo '<h2>Recent</h2><div class="video-contain">';
	foreach($recent as $r){
		$r['poster'] = str_replace(ROOT, '', $r['poster']);
		echo '<a href="';
		switch($r['data_type']){
			case 'tv':
				$slug = build_slug('view/'.$r['data_parent'], [], 'media');
				break;
			case 'video':
				$slug = build_slug('view/'.$r['data_id'], [], 'media');
				break;
			case 'audio':
				$slug = build_slug('album/'.$r['data_parent'], [], 'audio');
				break;
		}
		echo $slug;
		echo '" class="video-preview"><div class="img-contain"><img src="'.URI.$r['poster'].'"></div><h4>'.$r['data_name'];
		switch($r['data_type']){
			case 'tv':
			case 'audio':
				echo ' ('.$r['parent']['data_name'].')';
				break;
		}
		echo '</h4></a>';
	}
	echo '</div>';
}?>