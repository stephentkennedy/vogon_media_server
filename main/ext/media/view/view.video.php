<div class="container">
	<video poster="<?php echo URI.$poster; ?>" preload="metadata">
		<source src="<?php echo URI.$location; ?>" type="video/mp4">
	</video>
	<div id="title" class="active">
		<title class="title-bar"><?php echo $title; ?></title>
		<h1><i class="fa fa-arrow-circle-left back-to-view"></i> <span class="title-text"><?php echo $title; ?></span></h1>
	</div>
	<div id="video_preview">
		<h3 id="video_title">Next:</h3>
		<div id="img_container">
			<img id="video_img" src="/upload/thumbs/DB_001 - Secret Of The Dragon Balls-1.jpg">
			<h4 id="counter">15</h4>
		</div>
	</div>
	<div id="controls" class="active">
		<i class="fa fa-fw fa-play play"></i><input type="range" class="seek" value="0" max="" /><span id="time">0:00 / 0:00</span><i class="fa fa-fw fa-volume-up mute"></i><input type="range" class="volume" value="100" max="100" /><i class="fa fa-fw fa-expand fullscreen"></i>
	</div>
</div>
<style>
	video{
		width: 100vw;
		height: 100vh;
	}
	.container{
		position: relative;
	}
	#controls{
		position: absolute;
		width: 100%;
		bottom: 0;
		opacity: 0;
		transition: opacity 0.2s linear;
		height: 2.5rem;
		overflow: hidden;
		background: rgb(0,0,0);
		background: linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(5,5,5,1) 76%, rgba(100,100,100,1) 100%);
	}
	#controls i{
		cursor: pointer;
		padding: .5rem;
		width: 2rem;
	}
	#controls > *{
		vertical-align: middle;
		display: inline-block;
	}
	#controls input[type="range"]{
		display: inline-block;
		padding: .5rem;
		border: none !important;
		margin-top: 7px;
	}
	#controls .seek{
		width: calc(100% - calc(14rem + 104px));
	}
	#controls .volume{
		width: 100px;
	}
	#controls #time{
		width: 8rem;
		text-align: center;
	}
	#controls.active{
		opacity: 1;
	}
	#video_preview{
		position: absolute;
		top: 75%;
		transform: translateY(-50%);
		right: 20px;
		width: 10vw;
		min-width: 100px;
		opacity: 0;
		transition: opacity 0.2s linear;
		background-color: rgba(0,0,0,0.5);
		border: 1px solid rgba(var(--rgb-main-accent),1);
		pointer-events: none;
	}
	#video_preview #img_container{
		height: calc(.75 * 10vw);
		min-height: 75px;
		position: relative;
		overflow: hidden;
	}
	#video_preview.active{
		opacity: 1;
		cursor: pointer;
		pointer-events: all;
	}
	#video_preview #img_container #video_img{
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		min-width: 100%;
		max-width: 200%;
		min-height: 100%;
		max-height: 200%;
		width: auto;
		height: auto;
		transition: opacity 0.2s linear;
	}
	#video_preview:hover #img_container #video_img{
		opacity: 0.7;
	}
	#video_preview #video_title{
		margin: 0px;
		display: block;
		background-color: rgba(0,0,0,0.5);
		text-align: center;
		padding: 5px;
		font-size: .75rem;
	}
	#video_preview #counter{
		font-size: 4rem;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		margin-top: 0px;
		text-align: center;
		text-shadow: 2px 2px 2px #000;
	}
	#title{
		width: 100vw;
		position: absolute;
		top: 0;
		left: 0;
		opacity: 0;
		transition: opacity 0.2s linear;
		pointer-events: none;
	}
	#title.active{
		opacity: 1;
		pointer-events: all;
	}
	#title .title-bar{
		display: none;
	}
	#title h1{
		font-size: 1.5rem;
		color: #fff;
		text-shadow: 2px 2px 2px #000;
		padding: 10px;
		display: block;
		margin: 0;
	}
	#title h1 > *{
		vertical-align: middle;
		display: inline-block;
	}
	#title .back-to-view{
		cursor: pointer;
		opacity: 1;
		transition: opacity 0.2s linear;
		padding: 5px;
	}
	#title .back-to-view:hover{
		opacity: 0.75;
	}
</style>
<script type="text/javascript">
	var player = {
		video: false,
		controls: false,
		title: false,
		duration: 0,
		time: <?php echo $time; ?>,
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
			player.video[0].currentTime = player.time;
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
				}else{
					e.removeClass('fa-compress').addClass('fa-expand');
					document.exitFullscreen();
				}
			});
			
			player.video.on('loadedmetadata', function(){
				player.video.find('.seek').attr('max', player.video[0].duration);
				player.duration = player.timeFormat(player.video[0].duration);
				var display_time = player.timeFormat(player.time);
				player.controls.find('#time').html(display_time + ' / ' + player.duration);
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
			<?php if($series == ''){?>
			player.title.find('.back-to-view').click(function(){
				window.history.back();
			});
			<?php }?>
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
				}
				clearTimeout(player.inactive_timeout);
				player.inactive_timeout = setTimeout(function(){
					player.controls.removeClass('active');
					player.title.removeClass('active');
				}, 5000);
			});
			player.inactive_timeout = setTimeout(function(){
					player.controls.removeClass('active');
					player.title.removeClass('active');
				}, 5000);
		},
		updateHistory: function(){
			var data = {
				'id': player.id,
				'time': Number(player.video[0].currentTime)
			};
			$.get('/ajax/ajax_save_history/media', data, function(content){
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
			$.get('/ajax/ajax_get_series/media', data, function(content){
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