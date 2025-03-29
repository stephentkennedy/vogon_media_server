<div class="container" style="height: 100vh; width:100vw; overflow:hidden;">
	<video poster="<?php echo URI.$poster; ?>" preload="metadata" class="<?php if(!empty($animorphic)){
		echo 'animorphic';
	} ?>">
		<source src="<?php echo URI.$location; ?>" type="video/mp4">
		<?php 
			if(!empty($subtitles)){
				echo '<track kind="subtitles" srclang="en" src="'.$subtitles.'" default>';
			}
		?>
	</video>
	<div id="title" class="active">
		<title class="title-bar"><?php echo $title; ?></title>
		<h1><button class="fa fa-arrow-circle-left back-to-view"></button> <span class="title-text"><?php echo $title; ?></span></h1>
		<button class="fa fa-picture-o update-thumbnail"></button>
		<button class="fa fa-window-restore popout-1x" title="Pop out video at 1x resolution. This is useful for scaling applications."></button>
	</div>
	<div id="sub-controls" class="active <?php if(empty($subtitles)){
		 echo 'none';
	} ?>">
		<button class="subtitles">CC</button>
		<?php if(!empty($subtitles)){
		 echo '<div class="subtitles-container"><span class="holder"></span></div>';
	} ?>
	</div>
	<div id="video_preview">
		<h3 id="video_title">Next:</h3>
		<div id="img_container">
			<img id="video_img" src="">
			<h4 id="counter">15</h4>
		</div>
	</div>
	<div id="controls" class="active">
		<div class="control-row">
			<input type="range" class="seek" value="0" max="" />
			<span class="seek-counter hidden"></span>
			<span id="time">0:00 / 0:00</span>
		</div>
		<div class="control-row">
			<button class="fa fa-step-backward back-thirty"></button>
			<button class="fa fa-fw fa-play play"></button>
			<button class="fa fa-step-forward forward-thirty"></button>
			<button class="fa fa-fw fa-volume-up mute"></button>
			<input type="range" class="volume" value="100" max="100" />
			<button class="fa fa-fw fa-expand fullscreen" title="Toggle Fullscreen"></button>
			<button class="fa fa-fw fa-arrows-v animorphic-toggle" title="Toggle Animorphic Widescreen Display"></button>
		</div>
	</div>
	</div>
</div>
<style>
	::cue {
	color: transparent;
	background: transparent;
	}

</style>
<script type="text/javascript">
	var player = {
		video: false,
		controls: false,
		title: false,
		duration: 0,
		time: <?php echo $time; ?>,
		subtitles: false,
		fallbackVolume: 0,
		inactive_timeout: false,
		play_started: false,
		playing: false,
		id: <?php echo $id; ?>,
		h_loop: false,
		h_freq: <?php if(!empty($_SESSION['media_his_time'])){ echo $_SESSION['media_his_time']; }else{ echo '60000'; } ?>,
		init: function(){
			player.video = $('video');
			player.controls = $('#controls');
			player.title = $('#title');
			
			player.video.find('.seek').attr('max', player.video[0].duration);
			player.duration = player.timeFormat(player.video[0].duration);
			if(player.time < player.duration - 60){
				player.video[0].currentTime = player.time;
			}
			player.controls.find('#time').html('0:00 / ' + player.duration);
			
			player.video.on('play', function(){
				player.controls.find('.play').removeClass('fa-play').addClass('fa-pause');
				player.playing = true;
				clearTimeout(player.h_loop);
				player.h_loop = setTimeout(player.updateHistory, player.h_freq);
			});
			player.video.on('pause', function(){
				player.playing = false;
				player.controls.find('.play').removeClass('fa-pause').addClass('fa-play');
				clearTimeout(player.h_loop);
				//Because the first thing I do before changing devices is pause
				player.updateHistory();
			});
			
			player.controls.find('.play').click(function(){
				var p = $(this);
				if(p.hasClass('fa-pause')){
					player.video[0].pause();
				}else{
					player.video[0].play();
					if(player.play_started == false){
						player.play_started = true;
						player.activeFade();
					}
				}
			});
			player.controls.find('.forward-thirty').click(function(){
				if((player.video[0].currentTime + 30) < player.video[0].duration){
					player.video[0].currentTime += 30;
				}
			});
			
			player.controls.find('.back-thirty').click(function(){
				if((player.video[0].currentTime - 30) > 0){
					player.video[0].currentTime += -30;
				}else{
					player.video[0].currentTime = 0;
				}
			});
			
			player.controls.find('.animorphic-toggle').click(function(){
				if(player.video.hasClass('animorphic')){
					player.video.removeClass('animorphic');
				}else{
					player.video.addClass('animorphic');
				}
			});
			
			player.controls.find('.seek').change(function(){
				player.video[0].currentTime = $(this).val();
			});
			
			player.controls.find('.mute').click(function(){
				var v = $(this);
				if(v.hasClass('fa-volume-off')){
					player.video[0].volume = player.fallbackVolume;
					v.removeClass('fa-volume-off').addClass('fa-volume-up');
				}else{
					player.fallbackVolume = player.video[0].volume;
					player.video[0].volume = 0;
					v.removeClass('fa-volume-up').addClass('fa-volume-off');
				}
			});
			
			player.controls.find('.volume').change(function(){
				var newVol = $(this).val();
				newVol = newVol / 100;
				player.video[0].volume = newVol;
			});
			
			player.controls.find('.fullscreen').click(function(){
				var e = $(this);
				if(e.hasClass('fa-expand')){
					e.removeClass('fa-expand').addClass('fa-compress');
					$('.container')[0].requestFullscreen();
					screen.orientation.lock('landscape');
				}else{
					e.removeClass('fa-compress').addClass('fa-expand');
					document.exitFullscreen();
				}
			});
			
			player.video.on('loadedmetadata', function(){
				player.video.find('.seek').attr('max', player.video[0].duration);
				player.duration = player.timeFormat(player.video[0].duration);
				var display_time = player.timeFormat(player.video[0].currentTime);
				player.controls.find('#time').html(display_time + ' / ' + player.duration);
				//player.video[0].testTracks[0]
			});
			player.video.on('timeupdate', function(){
				var seek = player.controls.find('.seek');
				if(seek.attr('max') == ''){
					seek.attr('max', player.video[0].duration);
					player.duration = player.timeFormat(player.video[0].duration);
				}
				var curTime = Number(player.video[0].currentTime);
				seek.val(curTime);
				player.controls.find('#time').html(player.timeFormat(curTime)+' / '+player.duration);
			});
			player.sub_controls = $('#sub-controls');
			if(player.video.find('track')){
				player.track = player.video.find('track').prop('track');
				player.track_display = $('.subtitles-container .holder');
				$(player.track).on('cuechange', function(){
					var cue = $.prop(this, 'activeCues')[0];
					if(!cue || player.subtitles == false){
						var prev_text = player.track_display.html();
						if(prev_text != ''){
							player.track_display.html('');
						}
						return;
					}
					var text = cue.text;
					text = text.replace(/\n/g, '<br>');
					//console.log('Subtitle: '+ text);
					player.track_display.html(text);
				});
				$('#sub-controls .subtitles').click(function(){
					if(player.subtitles == false){
						player.subtitles = true;
						$(this).addClass('active');
						//player.video[0].textTracks[0].mode = 'showing';
					}else{
						player.subtitles = false;
						$(this).removeClass('active');
						//player.video[0].textTracks[0].mode = 'hidden';
					}
				});
			}
			$("#title button.popout-1x").click(function(){
				player.popout();
			});
			$('#title button.update-thumbnail').click(function(){
				$(this).removeClass('fa-picture-o');
				$(this).addClass('fa-cog').addClass('fa-spin');
				var seconds = player.video[0].currentTime;
				$.get(<?php echo "'".build_slug('ajax/ajax_thumbnail/media')."'"; ?>, {id: player.id, seconds: seconds}, function(returned){
					var string = returned.message;
					if(returned.thumbnail != ''){
						string += '<br><img style="max-width: 100%;height:auto;width:auto;" src="'+returned.thumbnail+'?time=<?php echo time(); ?>"><br>';
					}
					string += '<button class="button">Close</button>';
					var w = aPopup.newWindow(string);
					$('#title button.update-thumbnail').removeClass('fa-spin').removeClass('fa-cog').addClass('fa-picture-o');
					w.find('.button').click(function(){
						w.remove();
					});
				});
			});
			<?php if($series == ''){?>
			player.title.find('.back-to-view').click(function(){
				window.history.back();
			});
			<?php }?>
			
			$('.seek').mousemove(function(e){
				var seek = $(this);
				var span = $('.seek-counter');
				
				/*
				Name: Steph Kennedy
				Date: 2/22/21
				Comment: Now we do math to see what percentage we are in the bar.
				*/
				var bar_width = seek.width();
				var mouse_pos = e.pageX - seek.offset().left;
				var percent = mouse_pos / bar_width;
				var timecode = percent * player.video[0].duration;
				timecode = player.timeFormat(timecode);
				span.html(timecode);
				
				
				var y = ((seek.offset().top - $(window).scrollTop()) * -1) + window.innerHeight;
				var x = e.pageX - (span.width() / 2);
				if(span.hasClass('hidden')){
					span.removeClass('hidden');
				}
				span.attr('style', 'left:'+x+'px;bottom:'+y+'px;');
			});
			$('.seek').mouseleave(function(){
				$('.seek-counter').addClass('hidden');
			});
			
		},
		popout: function(){
			var screenHeight = window.screen.availHeight;
			var screenWidth = window.screen.availWidth;
			var height = window.outerHeight;
			var width = window.outerWidth;
			var heightDiff = screenHeight - height;
			var widthDiff = screenWidth - width;
			var video = player.video[0];
			if(video.videoHeight > 0){
				var vidWidth = video.videoWidth;
				var vidHeight = video.videoHeight;
				var newWinWidth = vidWidth + widthDiff;
				var newWinHeight = vidHeight + heightDiff;
				if(
					newWinHeight >= screenHeight
					|| newWinWidth >= screenWidth
				){
					//Exit if we're bigger than the screen we're playing on.
					return;
				}
				if(player.playing == true){
					player.video.trigger('pause');
				}
				var newWindow = window.open(window.location, "", "width="+newWinWidth+", height="+newWinHeight);
			}
		},
		timeFormat : function(duration){
			// Hours, minutes and seconds
			var hrs = ~~(duration / 3600);
			var mins = ~~((duration % 3600) / 60);
			var secs = ~~duration % 60;

			// Output like "1:01" or "4:03:59" or "123:03:59"
			var ret = "";

			if (hrs > 0) {
				ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
			}

			ret += "" + mins + ":" + (secs < 10 ? "0" : "");
			ret += "" + secs;
			return ret;
		},
		activeFade: function(){
			$('.container').mousemove(function(){
				if(!player.controls.hasClass('active')){
					player.controls.addClass('active');
					player.title.addClass('active');
					player.sub_controls.addClass('active');
				}
				clearTimeout(player.inactive_timeout);
				player.inactive_timeout = setTimeout(function(){
					player.controls.removeClass('active');
					player.title.removeClass('active');
					player.sub_controls.removeClass('active');
				}, 5000);
			});
			player.inactive_timeout = setTimeout(function(){
					player.controls.removeClass('active');
					player.title.removeClass('active');
					player.sub_controls.removeClass('active');
				}, 5000);
		},
		updateHistory: function(){
			var data = {
				'id': player.id,
				'time': Number(player.video[0].currentTime)
			};
			$.get('<?php echo build_slug("ajax/ajax_save_history/media");?>', data, function(content){
				if(content == 'saved' && player.playing == true){
					player.h_loop = setTimeout(player.updateHistory, player.h_freq);
				}else if (content != 'saved'){
					alert(content);
				}
			});
		}
	};
	
	$(document).ready(player.init);
</script>
<?php
if($series != ''){
?>
<script type="text/javascript">
	$(document).ready(function(){
		var data = {
			'name' : '<?php echo $series; ?>',
			'id' : '<?php echo $id; ?>'
		};
		
		autoplay.init(data);
	});
	
	
	var autoplay = {
		video: false,
		preview_dom: false,
		index: 0,
		list: [],
		counter: 0,
		counter_set: 10,
		counter_loop: false,
		next_seasion: false,
		url_seed: '<?php echo build_slug("watch", [], "media");?>',
		init: function(data){
			autoplay.video = $('video');
			autoplay.preview_dom = $('#video_preview');
			autoplay.video.off('click').click(function(){
				clearInterval(autoplay.counter_loop);
				autoplay.preview_dom.find('#counter').html('');
			});
			$.get('<?php echo build_slug("ajax/ajax_get_series/media"); ?>', data, function(content){
				autoplay.index = content.current;
				autoplay.list = content.list;
				autoplay.next_season = false;
				if(content.next_season != undefined){
					autoplay.next_season = content.next_season;
				}
				
				
				autoplay.video[0].onended = autoplay.preview;
			});
			player.title.find('.back-to-view').click(function(){
				window.location = '<?php echo build_slug("view/".$series_id, [], "media"); ?>';
			});
		},
		preview: function(){
			if(autoplay.list[autoplay.index + 1] != undefined){
				var next = autoplay.list[autoplay.index + 1];
				var title = next.name;
				var poster = next.poster;
			}else if(autoplay.next_season != false){
				var next = autoplay.next_season;
				var title = next.season_name + ': ' + next.episode_name
				var poster = next.poster;
			}else{
				return; //Return early because we're not doing anything.
			}
			var v = autoplay.preview_dom;
			v.find('#video_title').html(title);
			v.find('#video_img').prop('src', poster);
			autoplay.counter = autoplay.counter_set;
			v.find('#counter').html(autoplay.counter);
			autoplay.counter_loop = setInterval(autoplay.counter_func, 1000);
			
			v.off('click').click(function(){
				clearInterval(autoplay.counter_loop);
				v.removeClass('active');
				autoplay.next();
			});
			
			v.addClass('active');
		},
		counter_func: function(){
			autoplay.counter--;
			if(autoplay.counter > 0){
				autoplay.preview_dom.find('#counter').html(autoplay.counter);
			}else{
				clearInterval(autoplay.counter_loop);
				autoplay.preview_dom.removeClass('active');
				autoplay.next();
			}
		},
		next: function(){
			if(autoplay.list[autoplay.index + 1] != undefined){
				autoplay.index++;
				autoplay.load(autoplay.list[autoplay.index]['loc']);
				var title = autoplay.list[autoplay.index].name;
				var id = autoplay.list[autoplay.index].id;
				var poster = autoplay.list[autoplay.index].poster;
				var animorphic = autoplay.list[autoplay.index].animorphic;
				var subtitles = autoplay.list[autoplay.index].subtitles;
				player.video.removeClass('animorphic');
				if(animorphic == 1){
					player.video.addClass('animorphic');
				}
				if(typeof subtitles != 'undefined' && subtitles != '' && subtitles != false){
					player.video.find('track').prop('src', subtitles);
				}
				player.title.find('.title-bar').html(title);
				player.title.find('.title-text').html(title);
				autoplay.video.prop('poster', poster);
				history.pushState({}, '', autoplay.url_seed + '/' + id);
				player.id = id;
			}else if(autoplay.next_season != false){
				/*var data = {
					id: autoplay.next_season['episode_id']
				};*/
				//autoplay.load(autoplay.next_season['first_episode']);
				//autoplay.init(data);
				//We were trying to reinitialize everything without reloading, which would autoplay the next season, but since we couldn't get that working, for the moment we'll just reload the page to the next season. Users can click play.
				var id = autoplay.next_season['episode_id'];
				window.location = autoplay.url_seed + '/' + id;
			}
		},
		load: function(src){
			autoplay.video[0].pause();
			autoplay.video[0].currentTime = 0.0;
			autoplay.video.find('source').prop('src', src);
			autoplay.video[0].load();
			autoplay.video[0].play();
		}
	};
	window.onpopstate = function(e){
		//When we go back, parse the URL to take us to where it says
		window.location = window.location;
	}
</script>
<?php } ?>
