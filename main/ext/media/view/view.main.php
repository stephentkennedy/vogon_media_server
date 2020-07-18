<header>Video Library <a class="button" href="<?php echo build_slug('edit', [], 'media'); ?>">Add</a></header>
<div class="video-contain">
	<?php foreach($videos as $v){
		if(!isset($v['metas']['release'])){
			$v['metas']['release'] = 'Unknown';
		}
		echo '<a href="'.build_slug('view/'.$v['data_id'], [], 'media').'" class="video-preview"><div class="img-contain"><img src="'.URI.$v['metas']['thumb'].'"></div><h4>'.$v['data_name'].' ('.$v['metas']['release'].')</h4></a>';		
	} ?>

</div>