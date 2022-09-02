<div class="dir-container dir-container-<?php echo $_SESSION['active_filebrowsers']; ?>">
	<a class="file-link dir" data-loc="<?php echo $dir_up; ?>"><i class="fa fa-caret-up"></i> [Up a directory]</a>
	<?php 
		
	foreach($dirs as $d){
		echo '<a class="file-link dir" data-loc="'.$d['loc'].'"><i class="fa fa-folder"></i> '.$d['name'].'</a>';
	}
	foreach($files as $f){
		$temp = explode('/', $f['mime']);
		$temp = $temp[0];
		switch($temp){
			case 'text':
				$icon = 'fa-file-text-o';
				break;
			case 'video':
				$icon = 'fa-file-video-o';
				break;
			case 'audio':
				$icon = 'fa-file-audio-o';
				break;
			default:
				$icon = 'fa-file-o';
				break;
		}
		echo '<a class="file-link file" data-loc="'.$f['loc'].'" data-mime="'.$f['mime'].'"><i class="fa '.$icon.'"></i> '.$f['name'].'</a>';
	}
	?>
</div>