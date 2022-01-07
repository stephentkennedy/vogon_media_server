<header><h1><?php echo $artist; ?></h1></header>
<?php if(count($albums) > 0){ ?>
<div class="row">
	<?php foreach($albums as $album){
		echo '<div class="col col-two" style="margin-bottom: 2rem;"><a href="'.build_slug('album/'.$album['data_id'], [], 'audio').'">'.$album['data_name'].'</a></div>';
	} ?>
</div>
<?php }
if(count($loose_songs) > 0){ ?>
<article id="audio-library-artist" class="flex results">
	<div class="result-row result-header flex-row">
		<span class="result-one">Title</span>
		<span class="result-two">Length</span>
		<span class="result-three"></span>
	</div>
	<?php foreach($loose_songs as $r){
		if(empty($r['data_name'])){
			$filename = $r['data_content'];
			$filename = explode(DIRECTORY_SEPARATOR, $filename);
			$r['data_name'] = array_pop($filename);
		}
		if(empty($r['meta']['length'])){
			$length = '[Unknown]';
		}else{
			$length = formatLength($r['meta']['length']);
		}
		echo '<div class="result-row flex-row">';
		echo '<span class="result-one">'.$r['data_name'].'</span>';
		echo '<span class="result-two">'.$length.'</span>';
		echo '<span class="result-three">';
		echo '<a class="button miniplayer-play" data-id="'.$r['data_id'].'"><i class="fa fa-play"></i></a>';
		echo '<a class="button playlist-add" data-id="'.$r['data_id'].'"><i class="fa fa-plus"></i></a>';
		echo '<a class="button ajax-form" data-href="'.build_slug('edit/'.$r['data_id'], [], 'audio').'"><i class="fa fa-pencil"></i></a>';
		echo '</span>';
		echo '</div>';
	} ?>
</article>
<?php } ?>
