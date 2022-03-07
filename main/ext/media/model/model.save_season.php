<?php
	$test = $episodes[0];
	if(!empty($test)){
		$clerk = new clerk;
		$ep = $clerk->getRecord($test);
		$series = $ep['data_parent'];
		$sql = 'SELECT * FROM data, data_meta WHERE data.data_type = "season" AND data.data_parent = :parent AND data.data_id = data_meta.data_id AND data_meta.data_meta_name = "season_ord" AND data_meta.data_meta_content = :ord';
		$params = [
			':parent' => $series,
			':ord' => $order
		];
		$query = $db->query($sql, $params);
		$item = $query->fetch();
		if($item != false){
			$clerk->updateRecord(['name' => $name], $item['data_id']);
			$season_id = $item['data_id'];
		}else{
			$season_id = $clerk->addRecord([
				'name' => $name,
				'type' => 'season',
				'parent' => $series
			], [
				'season_ord' => $order
			]);
		}
		foreach($episodes as $ord => $ep){
			//debug_d('Updating Metas: '.$ep);
			$clerk->updateMetas($ep, ['season' => $season_id, 'episode_ord' => $ord]);
		}
		
		/*
		Name: Steph Kennedy
		Date: 2/26/2021
		Comment: When we update the season order, we'll need to clear the cache for that series.
		*/
		
		$sql = 'DELETE FROM cache WHERE cache_uri = :uri';
		$params = [
			':uri' => build_slug('view/'.$series, [] , 'media')
		];
		$db->query($sql, $params);
	}