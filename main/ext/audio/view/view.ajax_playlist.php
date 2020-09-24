<ol class="playlist">
	<?php $i = 0; foreach($songs as $s){ 
		if(empty($s['data_name'])){
			$filename = $s['data_content'];
			$filename = explode(DIRECTORY_SEPARATOR, $filename);
			$s['data_name'] = array_pop($filename);
		}
		$class = '';
		if($current == $i){
			$class = ' current';
		}
	?>
	<li class="playlist-track<?php echo $class; ?>" data-id="<?php echo $i; ?>" data-db-id="<?php echo $s['data_id']; ?>"><i class="fa fa-bars playlist-handle"></i><span class="name"><?php echo $s['data_name'];?></span><span class="duration"><?php echo formatLength($s['meta']['length']); ?></span><i class="fa fa-times playlist-remove"></i></li>
	<?php $i++; } ?>
</ol>