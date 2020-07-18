<header>
	<h1><?php echo $title; ?></h1>
</header>
<div class="row">
<?php
if(!empty($members['seasons'])){
	foreach($members['seasons'] as $s){?>
		<div class="col col-three">
		<h2><?php echo $s['name']; ?></h2>
		<ol class="video-list">
<?php foreach($s['episodes'] as $r){
			if(empty($r['series'])){
				$r['series'] = '';
			}
			if(empty($r['meta']['release'])){
				$r['meta']['release'] = '';
			}
			if(empty($r['data_name'])){
				$filename = $r['data_content'];
				$filename = explode(DIRECTORY_SEPARATOR, $filename);
				$r['data_name'] = array_pop($filename);
			}
			if($r['data_type'] == 'video'){
					$class = 'movie';
				}else{
					$class = 'tv-series';
				}
			if(!empty($r['meta']['poster'])){
				$r['meta']['poster'] = str_replace(ROOT, '', $r['meta']['poster']);
				echo '<li data-poster="'.$r['meta']['poster'].'">'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
			}else{
				echo '<li>'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
			}
		} ?>
		</ol>
		</div>
<?php }
}
if(!empty($members['tv'])){
?>
<div class="col col-three">
<a href="<?php echo build_slug('season_edit/'.$id, [], 'media'); ?>"><i class="fa fa-pencil"></i> Season Editor</a>
<h2>Unsorted Episodes:</h2>
<ol class="video-list"><?php
	foreach($members['tv'] as $r){
		if(empty($r['series'])){
			$r['series'] = '';
		}
		if(empty($r['meta']['release'])){
			$r['meta']['release'] = '';
		}
		if(empty($r['data_name'])){
			$filename = $r['data_content'];
			$filename = explode(DIRECTORY_SEPARATOR, $filename);
			$r['data_name'] = array_pop($filename);
		}
		if($r['data_type'] == 'video'){
				$class = 'movie';
			}else{
				$class = 'tv-series';
			}
		if(!empty($r['meta']['poster'])){
			$r['meta']['poster'] = str_replace(ROOT, '', $r['meta']['poster']);
			echo '<li data-poster="'.$r['meta']['poster'].'">'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
		}else{
			echo '<li>'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
		}
	}
?></ol></div>
<?php }
if(!empty($members['movies'])){?>
 <div class="col col-three">
 <h2>Movies:</h2>
	<ol class="video-list"><?php
	foreach($members['movies'] as $r){
		if(empty($r['series'])){
			$r['series'] = '';
		}
		if(empty($r['meta']['release'])){
			$r['meta']['release'] = '';
		}
		if(empty($r['data_name'])){
			$filename = $r['data_content'];
			$filename = explode(DIRECTORY_SEPARATOR, $filename);
			$r['data_name'] = array_pop($filename);
		}
		if($r['data_type'] == 'video'){
				$class = 'movie';
			}else{
				$class = 'tv-series';
			}
		if(!empty($r['meta']['poster'])){
			$r['meta']['poster'] = str_replace(ROOT, '', $r['meta']['poster']);
			echo '<li data-poster="'.$r['meta']['poster'].'">'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
		}else{
			echo '<li>'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
		}
	}
?></ol></div>
<?php }?>
</div>