<ol>
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
	<li class="playlist-track<?php echo $class; ?>" data-id="<?php echo $i; ?>"><span class="name"><?php echo $s['data_name'];?></span><span class="duration"><?php echo formatLength($s['meta']['length']); ?></span></li>
	<?php $i++; } ?>
</ol>