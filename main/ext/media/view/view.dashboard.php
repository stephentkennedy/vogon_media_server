<header><h1>Dashboard</h1></header>
<?php if(!empty($recent)){
	echo '<h2>Recent</h2><div class="video-contain">';
	foreach($recent as $r){
		if(!empty($r['poster'])){
			$r['poster'] = str_replace(ROOT, '', $r['poster']);
		}else{
			$r['poster'] = '';
		}
		echo '<a href="';
		$thumbnail = URI.$r['poster'];
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
			case 'cbz':
			case 'pdf':
				$slug = build_slug('view/'.$r['data_id'], ['page' => $r['history_val']], 'ebooks');

				break;
			case 'epub':
				$slug = build_slug('view/'.$r['data_id'], [], 'ebooks');
				break;
		}
		echo $slug;
		echo '" class="video-preview"><div class="img-contain">';
		switch($r['data_type']){
			case 'video':
			case 'tv':
				echo '<img src="'.$thumbnail.'">';
				break;
			case 'cbz':
				echo '<img class="lazy" src="" data-id="'.$r['data_id'].'" data-page="'.$r['history_val'].'">';
				break;
			default:
				echo '<i class="fa fa-play-circle"></i>';
		}
		
		if(empty($r['data_name'])){
			$filename = $r['data_content'];
			$filename = explode(DIRECTORY_SEPARATOR, $filename);
			$r['data_name'] = array_pop($filename);
		}

		echo '</div><h4>'.$r['data_name'];
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
<script type="text/javascript">
	var dashboard_lazy = {
		url: '<?php echo build_slug('ajax/ajax_cb_image_data/ebooks'); ?>',
		get_img: function(dom){
			var $item = $(dom);
			var id = $item.data('id');
			var page = $item.data('page');
			$.get(dashboard_lazy.url, {
				id: id,
				page: page
			}, function(returned){
				if((returned.error == undefined || returned.error == false) && returned != '' && returned.image_data != undefined){
					$item.attr('src', returned.image_data);
				}
			});
		},
		init: function(){
			var lazy_imgs = $('img.lazy');
			for(i in lazy_imgs){
				if(isInt(i)){
					dashboard_lazy.get_img(lazy_imgs[i]);
				}
			}
		}
	};
	$(document).ready(function(){
		dashboard_lazy.init();
	});
</script>