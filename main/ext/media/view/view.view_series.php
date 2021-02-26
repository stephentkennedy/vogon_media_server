<header>
	<h1><?php echo $title; ?> <a href="<?php echo build_slug('season_edit/'.$id, [], 'media'); ?>"><i class="fa fa-pencil"></i> Season Editor</a></h1>
</header>
<div class="row">
	<div class="col col-ten">
		<a data-id="<?php echo $id; ?>" id="next_series"><i class="fa fa-spin fa-cog"></i> Checking ...</a>
	</div>
<?php
//debug_d($data);
if(!empty($members['seasons'])){
	foreach($members['seasons'] as $s){?>
		<div class="col col-five">
		<h2><?php echo $s['name']; ?></h2>
		<ol class="video-list">
<?php foreach($s['episodes'] as $r){
			//debug_d($r);die();
			if(empty($r['series'])){
				$r['series'] = '';
			}
			if(empty($r['release'])){
				$r['release'] = '';
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
			if(!empty($r['poster'])){
				$r['poster'] = str_replace(ROOT, '', $r['poster']);
				echo '<li>'.$r['data_name'].' ('.formatLength($r['length']).')<br><img class="series_poster" data-src="'.$r['poster'].'">';
				
				//if($r['time'] == false){
					$percent = 0;
				//}else{
				//	$percent = ceil(($r['time'] / $r['meta']['length']) * 100);
				//}
				
				echo '<div data-id="'.$r['data_id'].'" data-length="'.$r['length'].'" class="episode_progress"><div style="width: '.$percent.'%" class="progress_bar"></div></div>';
				echo '<a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a></li>';
			}else{
				echo '<li>'.$r['data_name'].'<br>Test<a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
			}
		} ?>
		</ol>
		</div>
<?php }
}
if(!empty($members['tv'])){
?>
<div class="col col-five">
<h2>Unsorted Episodes:</h2>
<ol class="video-list"><?php
	foreach($members['tv'] as $r){
		if(empty($r['series'])){
			$r['series'] = '';
		}
		if(empty($r['release'])){
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
		if(!empty($r['poster'])){
			$r['poster'] = str_replace(ROOT, '', $r['poster']);
			echo '<li>'.$r['data_name'].' ('.formatLength($r['length']).')<br><img class="series_poster" data-src="'.$r['poster'].'">';
			
			//if($r['time'] == false){
				$percent = 0;
			//}else{
			//	$percent = ceil(($r['time'] / $r['meta']['length']) * 100);
			//}
			
			echo '<div data-id="'.$r['data_id'].'" data-length="'.$r['length'].'" class="episode_progress"><div style="width: '.$percent.'%" class="progress_bar"></div></div>';
			echo '<a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a></li>';
		}else{
			echo '<li>'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
		}
	}
?></ol></div>
<?php }
if(!empty($members['movies'])){?>
 <div class="col col-five">
 <h2>Movies:</h2>
	<ol class="video-list"><?php
	foreach($members['movies'] as $r){
		if(empty($r['series'])){
			$r['series'] = '';
		}
		if(empty($r['release'])){
			$r['release'] = '';
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
		if(!empty($r['poster'])){
			$r['poster'] = str_replace(ROOT, '', $r['poster']);
			echo '<li>'.$r['data_name'].' ('.formatLength($r['length']).')<br><img class="series_poster" data-src="'.$r['poster'].'">';
			
			//if($r['time'] == false){
				$percent = 0;
			//}else{
			//	$percent = ceil(($r['time'] / $r['meta']['length']) * 100);
			//}
			
			echo '<div data-id="'.$r['data_id'].'" data-length="'.$r['length'].'" class="episode_progress"><div style="width: '.$percent.'%" class="progress_bar"></div></div>';
			echo '<a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a></li>';

		}else{
			echo '<li>'.$r['data_name'].'<br><a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-link '.$class.'" ><i class="fa fa-info"></i> Details</a><a href="'.build_slug('watch/'.$r['data_id'], [], 'media').'"><i class="fa fa-play"></i> Play</a></li>';
		}
	}
?></ol></div>
<?php }?>
</div>
<script type="text/javascript">
	var vogon_history = {
		ident: '.episode_progress',
		loop: function(){
			var items = $(vogon_history.ident+':not(.loaded)');
			items.each(function(i){
				var dom = items[i];
				if(lazy.check(dom)){
					vogon_history.load(dom);
				}else{
				}
			});
		},
		load: function(dom){
			var id = $(dom).data('id');
			$.get('<?php echo build_slug("ajax/ajax_history/media"); ?>', {'id': id}).done(function(returned){
				var watched = returned['watched'];
				var total = Number($(dom).data('length'));
				var percent = watched / total;
				percent = percent * 100;
				percent = Math.ceil(percent);
				$(dom).find('.progress_bar').css('width', percent + '%');
				$(dom).addClass('loaded');
			});
		},
		init: function(){
			$(window).off('resize.history').on('resize.history', function(){
				vogon_history.init();
			});
			$(window).off('scroll.history').on('scroll.history', vogon_history.loop);
			vogon_history.loop();
		},
		getNext: function(){
			var id = $('#next_series').data('id');
			$.get('<?php echo build_slug("ajax/ajax_get_next/media"); ?>', {id: id}).done(function(returned){
				if(returned.result == true){
					var link = $('#next_series');
					link.html(returned.text);
					link.attr('href', '<?php echo build_slug("watch/", [], "media"); ?>'+returned.id);
				}else{
					var parent = $('#next_series').parent();
					$('#next_series').remove();
					parent.append('<p>You haven&#39;t watched this series yet.</p>');
					console.log(returned);
				}
			});
		}
	};
	$(document).ready(function(){
		lazy.init('.series_poster');
		vogon_history.init();
		vogon_history.getNext();
	});
</script>