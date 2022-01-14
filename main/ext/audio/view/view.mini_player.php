<div class="tray right col col-three">
	<div class="controls">
		<i class="fa fa-list tray-expand" title="Playlists"></i>
	</div>
	<div class="container">
		<h2>Playlists</h2>
		<div id="playlists" class="content row">
		</div>
	</div>
</div>
<div id="current" class="tray right second col col-three">
	<div class="controls">
		<i class="fa fa-list-alt tray-expand" title="Now Playing"></i><span class="counter hidden"></span>
	</div>
	<div class="container" id="now_playing">
		<h2 id="title">Now Playing <i class="fa fa-floppy-o button playlist-save" title="save"></i> <i class="fa fa-file-o button playlist-new" title="New Playlist"></i></h2>
		<div id="current-playlist" class="content row">
		</div>
	</div>
</div>
<audio class="miniplayer-preload" preload="true"><source class="next-source"></source></audio>
<script type="text/javascript">
var miniplayer = {
};
$(document).ready(function(){
	playlist.fetch();
	playlist.bind();
	miniplayer = {
		title: $('title').html(),
		instance: {},
		audio: {},
		id: {},
		cur_id: false,
		next_id: false,
		next: {},
		src: {},
		header: {},
		trueColor: {
			r: 200,
			g: 200,
			b: 0
		},
		curColor: false,
		colorHold: 200,
		colorFrame: 0,
		colorStep: 0.05,
		angle: 0,
		binCount: 0,
		binPercent: .75,
		track: false,
		playing: false,
		fDur: false,
		h_loop: false,
		h_freq: <?php if(!empty($_SESSION['audio_his_time'])){ echo $_SESSION['audio_his_time']; }else{ echo '10000'; } ?>,
		sleep_timer: false,
		animation: miniplayer.<?php if(!empty($_SESSION['def_visual'])){ echo $_SESSION['def_visual']; }else{ echo 'noViz'; } ?>,
		seed: '<div class="mini-player"><header class="mini-player-header"><span class="shadow"></span><span class="controls"><i class="fa fa-window-maximize toggle"></i><i class="fa fa-cog option"></i><i class="fa fa-expand fullscreen"></i><i class="fa fa-times close"></i></span></header><info><a target="_blank" data-target="popup" class="mini-player-label title"></a><a target="_blank" data-target="popup" class="mini-player-label album"></a><a target="_blank" data-target="popup" class="mini-player-label band"></a></info><canvas></canvas><audio class="current-track"><source class="current-source" onerror="miniplayer.error"></source></audio><div class="mini-player-audio-controls"><i class="fa fa-heart-o favorite"></i><input type="range" class="seek" value="0" max="" /><span class="mini-player-seek-counter hidden"></span> <span class="mini-player-counter">0:00 / 0:00</span> <br><i class="fa fa-random  fa-fw mini-player-shuffle disable"></i><i class="fa fa-step-backward fa-fw  mini-player-prev disable"></i><i class="fa fa-play fa-fw  mini-player-play"></i><i class="fa fa-step-forward fa-fw  mini-player-next disable"></i><i class="fa fa-retweet fa-fw  mini-player-loop disable"></i><span class="mini-one">1</span></div><i class="fa fa-clock-o sleep-timer"></i><div class="mini-player-playlist"></div></div>',
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
		refresh_labels: function(){
			$.get('<?php echo build_slug("ajax/ajax_audio/audio/"); ?>' + miniplayer.cur_id, function( returned ){
				data = returned;
				miniplayer.header.html('<span>'+data['title']+'</span>');
				miniplayer.instance.find('.mini-player-label.title').html(data['title']).attr('data-href', data['link']);
				$('title').html(miniplayer.title + ': ' + data['title']);
				if(data['artist'] != null && data['artist'] != ''){
					miniplayer.instance.find('.mini-player-label.band').html(data['artist']);
				}else{
					miniplayer.instance.find('.mini-player-label.band').html('').attr('data-href', '');
				}
				if(data['album'] != null && data['album'] != ''){
					miniplayer.instance.find('.mini-player-label.album').html(data['album']).attr('data-href', data['album_link']);
				}else{
					miniplayer.instance.find('.mini-player-label.album').html('').attr('data-href', '');
				}
				if(miniplayer.header.find('span').width() > miniplayer.header.width()){
					miniplayer.header.find('span').addClass('marquee');
				}
				if(data['favorite'] != undefined && data['favorite'] == true){
					miniplayer.instance.find('.favorite').removeClass('fa-heart-o').addClass('fa-heart');
				}else{
					miniplayer.instance.find('.favorite').removeClass('fa-heart').addClass('fa-heart-o');
				}
			});
		},
		preload: function(id){
			var loader = $('.miniplayer-preload .next-source');
			if(id != false && id != miniplayer.cur_id){
				miniplayer.next_id = id;
				$.get('<?php echo build_slug("ajax/ajax_audio/audio/"); ?>' + id, function( returned ){
					data = returned;
					loader.prop('src', data['src']);
					if(data['mime'] == 'application/octet-stream'){
						//then we have a bad mime-header somewhere
						//As documented elsewhere this happens most often with mp3 files.
						//So we'll just override the bad mime with the correct one for mp3 files.
						data['mime'] = 'audio/mpeg';
					}
					loader.prop('type', data['mime']);
					miniplayer.next = data;
				});
			}else{
				miniplayer.next_id = false;
				miniplayer.next = {};
				loader.prop('src', '');
				loader.prop('type', '');
			}
		},
		load: function(id){
			if(miniplayer.instance == false){
				miniplayer.init();
			}
			if(miniplayer.next_id != false && miniplayer.next_id == id){
				miniplayer.load_bind(miniplayer.next);
				miniplayer.preload(false);
			}else{
				$.get('<?php echo build_slug("ajax/ajax_audio/audio/"); ?>' + id, function( returned ){
					miniplayer.load_bind(returned);
				});
			}
		},
		load_bind: function(returned){
			data = returned;
			miniplayer.cur_id = data['id'];
			miniplayer.audio[0].pause();
			miniplayer.src.prop('src', data['src']);
			if(data['mime'] == 'application/octet-stream'){
				//then we have a bad mime-header somewhere
				//As documented elsewhere this happens most often with mp3 files.
				//So we'll just override the bad mime with the correct one for mp3 files.
				data['mime'] = 'audio/mpeg';
			}
			miniplayer.src.prop('type', data['mime']);
			miniplayer.id = data['id'];
			miniplayer.header.html('<span>'+data['title']+'</span>');
			miniplayer.instance.find('.mini-player-label.title').html(data['title']).attr('data-href', data['link']);
			$('title').html(miniplayer.title + ': ' + data['title']);
			if(data['artist'] != null && data['artist'] != ''){
				miniplayer.instance.find('.mini-player-label.band').html(data['artist']);
			}else{
				miniplayer.instance.find('.mini-player-label.band').html('');
			}
			if(data['album'] != null && data['album'] != ''){
				miniplayer.instance.find('.mini-player-label.album').html(data['album']).attr('href', data['album_link']);
			}else{
				miniplayer.instance.find('.mini-player-label.album').html('').attr('href', '');
			}
			$('.mini-player-label.title').off().click(function(){
				if($(this).attr('data-href') != ''){
					$(this).prepend('<i class="fa fa-fw fa-cog fa-spin"></i> ... ');
					var data = {
						format: 'ajax_form'
					};
					$.get($(this).attr('data-href'), data, function( returned ){
						var html = $('.mini-player-label.title').html();
						html = html.replace('<i class="fa fa-fw fa-cog fa-spin"></i> ... ', '');
						$('.mini-player-label.title').html(html);
						app.ajax_form(returned, function(){
							miniplayer.refresh_labels();
							controller.refresh_labels(miniplayer.cur_id);
							$('.mini-player-label.title').html();
						});
					});
				}
			});
			if(miniplayer.header.find('span').width() > miniplayer.header.width()){
				miniplayer.header.find('span').addClass('marquee');
			}
			miniplayer.audio[0].load();
			if(data['duration'] !== undefined){
				var duration = Number(data['duration']);
				miniplayer.fDur = miniplayer.timeFormat(duration);
			}
			if(data['time'] == undefined || data['time'] == 0){
				miniplayer.currentTime = 0.0;
				miniplayer.audio[0].currentTime = 0.0;
				miniplayer.instance.find('.mini-player-counter').html('0:00 / ' + miniplayer.fDur);
			}else{
				var min = 60;
				if(data['time'] <= (data['duration'] - .5 * min)){
					miniplayer.currentTime = data['time'];
					miniplayer.audio[0].currentTime = data['time'];
				}else{
					miniplayer.currentTime = 0.0;
					miniplayer.audio[0].currentTime = 0.0;
				}
				var friendly = miniplayer.timeFormat(miniplayer.audio[0].currentTime);
				miniplayer.instance.find('.mini-player-counter').html(friendly + ' / ' + miniplayer.fDur);
			}
			if(data['time'] == undefined){
				miniplayer.track = false;
			}else{
				miniplayer.track = true;
			}
			if(data['favorite'] != undefined && data['favorite'] == true){
				miniplayer.instance.find('.favorite').removeClass('fa-heart-o').addClass('fa-heart');
			}else{
				miniplayer.instance.find('.favorite').removeClass('fa-heart').addClass('fa-heart-o');
			}
			miniplayer.audio[0].play();
			if('mediaSession' in navigator){
				navigator.mediaSession.metadata = new MediaMetadata({
					title: data['title'],
					artist: data['artist'],
					album: data['album']
				});
				if('MediaPositionState' in navigator.mediaSession){
					navigator.mediaSession.setPositionState({
					duration: miniplayer.audio[0].duration,
					playbackRate: 1,
					position: miniplayer.audio[0].currentTime
				});
				}
			}
		},
		chooseColor: function(){
			var r = Math.floor(Math.random() * 155) + 100;
			var g = Math.floor(Math.random() * 155) + 100;
			var b = Math.floor(Math.random() * 155) + 100;
			miniplayer.trueColor = {
				r: r,
				g: g,
				b: b
			};
		},
		toggle: function(){
			if(miniplayer.instance.hasClass('open')){
				miniplayer.instance.removeClass('open');
				miniplayer.instance.find('.fa.toggle').removeClass('fa-window-minimize').addClass('fa-window-maximize');
			}else{
				miniplayer.instance.addClass('open');
				miniplayer.instance.find('.fa.toggle').removeClass('fa-window-maximize').addClass('fa-window-minimize');
			}
		},
		remove: function(){
			miniplayer.audio[0].pause();
			miniplayer.instance.remove();
			miniplayer.instance = false;
		},
		play: function(){
			if(miniplayer.instance.find('.fa.play').hasClass('fa-play')){
				miniplayer.audio[0].play();
				if('mediaSession' in navigator){
					navigator.mediaSession.playbackState = 'playing';
				}
			}else{
				miniplayer.audio[0].pause();
				if('mediaSession' in navigator){
					navigator.mediaSession.playbackState = 'paused';
				}
			}
		},
		updateHistory: function(){
			if(miniplayer.track == true){
				var data = {
					'id': miniplayer.id,
					'time': Number(miniplayer.audio[0].currentTime)
				};
				$.get('<?php echo build_slug("ajax/ajax_save_history/media"); ?>', data, function(content){
					if(content == 'saved' && miniplayer.playing == true){
						miniplayer.h_loop = setTimeout(miniplayer.updateHistory, miniplayer.h_freq);
					}else if (content != 'saved'){
						alert(content);
					}
				});
			}
		},
		toggleFavorite: function(){
			if(miniplayer.id == undefined || miniplayer.id == false){
				return false;
			}
			var data = {
				'id': miniplayer.id
			};
			$.get('<?php echo build_slug('ajax/ajax_toggle_favorite/audio'); ?>', data, function(content){
				if(content['saved'] != undefined && content['saved'] == true){
					miniplayer.refresh_labels();
					controller.refresh_labels(miniplayer.id);
				}else{
					alert(content);
				}
			});
		},
		rgbMe: function(colorObj){
			var string = 'rgb(' + colorObj.r + ',' + colorObj.g + ',' + colorObj.b + ')';
			return string;
		},
		drawBar: function (x1, y1, x2, y2, width,frequency){
			var freq_temp = frequency / 255;
			if(freq_temp > 1){
				freq_temp = 1;
			}
			var color = {
				r: freq_temp * miniplayer.curColor.r,
				g: freq_temp * miniplayer.curColor.g,
				b: freq_temp * miniplayer.curColor.b
			}
			
			//var lineColor = "rgb(" + color.r + ", " + color.g + ", " + color.b + ")";
			var lineColor = miniplayer.rgbMe(color);
			
			ctx.strokeStyle = lineColor;
			ctx.lineWidth = width;
			ctx.beginPath();
			ctx.moveTo(x1,y1);
			ctx.lineTo(x2,y2);
			ctx.stroke();
		},
		drawBarSolid: function (x1, y1, x2, y2, width,frequency){
			var lineColor = "rgb("+miniplayer.curColor.r+","+miniplayer.curColor.g+","+miniplayer.curColor.b+")";
    
			ctx.strokeStyle = lineColor;
			ctx.lineWidth = width;
			ctx.beginPath();
			ctx.moveTo(x1,y1);
			ctx.lineTo(x2,y2);
			ctx.stroke();
		},
		cTransition: function(){
			var to = miniplayer.trueColor;
			var cur = miniplayer.curColor;
			if(to.r == ~~cur.r && to.g == ~~cur.g && to.b == ~~cur.b){
				miniplayer.curColor = {
					r: ~~cur.r,
					g: ~~cur.g,
					b: ~~cur.b
				};
				return false;
			}
			if(to.r != cur.r){
				if(to.r > ~~cur.r){
					cur.r +=  miniplayer.colorStep;
				}else{
					cur.r +=  miniplayer.colorStep * -1;
				}
			}else{
				cur.r = ~~cur.r;
			}
			if(to.g != ~~cur.g){
				if(to.g > cur.g){
					cur.g +=  miniplayer.colorStep;
				}else{
					cur.g +=  miniplayer.colorStep * -1;
				}
			}else{
				cur.g = ~~cur.g;
			}
			if(to.b != ~~cur.b){
				if(to.b > ~~cur.b){
					cur.b +=  miniplayer.colorStep;
				}else{
					cur.b +=  miniplayer.colorStep * -1;
				}
			}else{
				cur.b = ~~cur.b;
			}
			miniplayer.curColor = cur;
			return true;
		},
		colorControl: function(){
			var check = miniplayer.cTransition();
			if(check == false){
				//console.log('Holding: '+miniplayer.colorFrame+ ' of ' + miniplayer.colorHold);
				if(miniplayer.colorFrame == 0){
					miniplayer.colorHold = Math.floor(Math.random() * (2000));
				}
				if(miniplayer.colorHold > miniplayer.colorFrame){
					miniplayer.colorFrame++;
				}else{
					miniplayer.colorFrame = 0;
					miniplayer.chooseColor();
				}
			}else{
				
			}
		},
		getCompliment: function(colorObj){
			//Floor them so we can deal with scaling colors.
			var r = ~~colorObj.r;
			var g = ~~colorObj.g;
			var b = ~~colorObj.b;
			return {
				r: (255 - r),
				g: (255 - g),
				b: (255 - b)
			};
		},
		options: function(){
			var viz = {
				'noViz': {
					name:'None',
					func: miniplayer.noViz
				},
				'spectro': {
					name: 'Spectrograph',
					func: miniplayer.spectro
				},
				'bars': {
					name: 'Bars',
					func: miniplayer.bars
				},
				'circle': {
					name: 'Warp',
					func: miniplayer.cleanCircle
				},
				/*'burnout': {
					name: 'Burnout',
					func: miniplayer.burnout
				}*/
			}
			var string = '<label for="visualizer">Visualization</label><select id="visualizer">';
			for(i in viz){
				var selected = '';
				if(viz[i].func == miniplayer.animation){
					selected = ' selected';
				}
				string += '<option value="'+i+'"'+selected+'>'+viz[i].name+'</option>';
			}
			string += '</select><br><button class="confirm"><i class="fa fa-floppy-o"></i> Save</button>';
			var win = aPopup.newWindow(string);
			win.find('.confirm').click(function(){
				var anim = win.find('#visualizer').val();
				switch(anim){
					case 'spectro':
						miniplayer.animation = miniplayer.spectro;
						window.localStorage.setItem('vizualizer', 'spectro');
						break;
					case 'bars':
						miniplayer.animation = miniplayer.bars;
						window.localStorage.setItem('vizualizer', 'bars');
						break;
					case 'circle':
						miniplayer.animation = miniplayer.cleanCircle;
						window.localStorage.setItem('vizualizer', 'cleanCircle');
						break;
					case 'burnout':
						miniplayer.animation = miniplayer.burnout;
						window.localStorage.setItem('vizualizer', 'burnout');
						break;
					case 'noViz':
						miniplayer.animation = miniplayer.noViz;
						window.localStorage.setItem('vizualizer', 'noViz');
						break;
				}
				win.remove();
			});
		},
		fullscreen: function(){
			var canvas = miniplayer.instance.find('canvas')[0];
			if(miniplayer.instance.hasClass('fullscreen')){
				miniplayer.instance.removeClass('fullscreen');
				var width = miniplayer.instance.width();
				canvas.width = width;
				canvas.height = 150;
			}else{
				miniplayer.instance.addClass('fullscreen');
				var width = miniplayer.instance.width();
				var height = miniplayer.instance.height() - 158;
				canvas.width = width;
				canvas.height = height;
			}
		},
		noViz: function(){
			if(miniplayer.instance.hasClass('open')){
				canvas = miniplayer.instance.find('canvas')[0];
				ctx = canvas.getContext("2d");
				ctx.globalCompositeOperation = 'source-over';
				ctx.fillStyle = '#000000';
				ctx.fillRect(0,0,canvas.width,canvas.height);
			}
			window.requestAnimationFrame(miniplayer.animation);
		},
		cleanCircle: function (){
			if(miniplayer.instance.hasClass('open')){
				miniplayer.colorControl();
				// set to the size of device
				canvas = miniplayer.instance.find('canvas')[0];
				//canvas.width = miniplayer.instance.find('canvas').width();
				//canvas.height = ;
				ctx = canvas.getContext("2d");
				ctx.globalCompositeOperation = 'source-over';
				ctx.fillStyle = '#000000';
				ctx.fillRect(0,0,canvas.width,canvas.height);
				
				// find the center of the window
				center_x = canvas.width / 2;
				center_y = canvas.height / 2;
				
				
				// style the background
				
				analyser.getByteFrequencyData(frequency_array);
				
				var gradient = ctx.createLinearGradient(0,0,0,canvas.height);
				
				var level = 0;
				
				/*
				Name: Stephen Kennedy
				Date: 2/17/21
				Comment: Right now the visualizers only sample the top of the array, they need to be modified to sample the whole array so that we get visualization of the highs, mids, and lows, when right now we're just getting highs and some mides.
				*/
				
				var bar_increment = ~~(miniplayer.binCount / bars);
				
				for(var i = 0; i < miniplayer.binCount; i += bar_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					level += frequency_array[i];
					//console.log(i, frequency_array[i]);
				}
				level = Math.floor(level / bars);
				radius = Math.floor(level / 8);
				
				var comp = miniplayer.getCompliment(miniplayer.curColor);
				var lvl_temp = ~~(level / 2) / 255;
				
				gradient.addColorStop(0,"rgba("+(comp.r * lvl_temp)+","+(comp.g * lvl_temp)+", "+(comp.b * lvl_temp)+", 1)");
				
				gradient.addColorStop(1,"rgba(0, 0, 0, 1)");
				ctx.fillStyle = gradient;
				ctx.fillRect(0,0,canvas.width,canvas.height);
				ctx.fillStyle = 'transparent';
				
				//draw a circle
				//ctx.strokeStyle = 'rgba(150,0,100,1)';
				ctx.beginPath();
				ctx.arc(center_x,center_y,radius,0,2*Math.PI);
				ctx.stroke();
				var angle = 0;
				for(var i = 0; i < miniplayer.binCount; i += bar_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					//divide a circle into equal parts
					rads = Math.PI * 2 / (miniplayer.binCount / bar_increment);
					
					bar_height = frequency_array[i]*(center_y / center_x) + ((center_y / 2) - radius);
					
					// set coordinates
					x = center_x + Math.cos(rads * angle) * (radius);
				y = center_y + Math.sin(rads * angle) * (radius);
					x_end = center_x + Math.cos(rads * angle)*(radius + bar_height);
					y_end = center_y + Math.sin(rads * angle)*(radius + bar_height);
					
					//draw a bar
					miniplayer.drawBar(x, y, x_end, y_end, bar_width,frequency_array[i]);
					angle++;
				}
			}
			window.requestAnimationFrame(miniplayer.animation);
		},
		burnout: function(){
			if(miniplayer.instance.hasClass('open')){
				miniplayer.colorControl();
				// set to the size of device
				canvas = miniplayer.instance.find('canvas')[0];
				//canvas.width = miniplayer.instance.find('canvas').width();
				//canvas.height = ;
				ctx = canvas.getContext("2d");
				ctx.globalCompositeOperation = 'source-over';
				
				// find the center of the window
				center_x = canvas.width / 2;
				center_y = canvas.height / 2;
				
				
				// style the background
				
				analyser.getByteFrequencyData(frequency_array);
				
				var gradient = ctx.createLinearGradient(0,0,0,canvas.height);
				
				var level = 0;
				
				var bar_increment = ~~(miniplayer.binCount / bars);
				
				for(var i = 0; i < miniplayer.binCount; i += bar_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					level += frequency_array[i];
				}
				level = Math.floor((level / bars) / 4);
				
				radius = level;
				
				var temp_width = canvas.width + .01;
				var temp_height = canvas.height + .01;
				
				ctx.translate(center_x, center_y);
				miniplayer.angle += 0.00005;
				if(miniplayer.angle > 360){
					miniplayer.angle = 0;
				}
				var radian = miniplayer.angle * Math.PI / 180;
				ctx.rotate(radian);
				ctx.drawImage(canvas, Math.floor(temp_width / 2) * -1, Math.floor(temp_height / 2) * -1, temp_width, temp_height);
				ctx.globalCompositeOperation = 'source-over';
				ctx.rotate(-1 * radian);
				ctx.setTransform(1, 0, 0, 1, 0, 0);
				
				var comp = miniplayer.getCompliment(miniplayer.curColor);
				var lvl_temp = level / 255;
				
				gradient.addColorStop(0,"rgba("+(comp.r * lvl_temp)+","+(comp.g * lvl_temp)+", "+(comp.b * lvl_temp)+", 1)");
				gradient.addColorStop(1,"rgba(0, 0, 0, 0)");
				ctx.fillStyle = gradient;
				//ctx.fillRect(0,0,canvas.width,canvas.height);
				ctx.fillStyle = 'transparent';
				
				//draw a circle
				ctx.filleStyle = 'rgba(0,0,0,1)';
				ctx.beginPath();
				ctx.arc(center_x,center_y,radius,0,2*Math.PI);
				ctx.fill();
				var angle = 0;
				for(var i = 0; i < miniplayer.binCount; i += bar_increment){
					
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					
					//divide a circle into equal parts
					rads = Math.PI * 2 / (miniplayer.binCount / bar_increment);
					
					bar_height = frequency_array[i]*0.7 + (150 - level);
					
					// set coordinates
					x = center_x + Math.cos(rads * angle) * (radius);
					y = center_y + Math.sin(rads *anglei) * (radius);
					x_end = center_x + Math.cos(rads * angle)*(radius + bar_height);
					y_end = center_y + Math.sin(rads * angle)*(radius + bar_height);
					
					//draw a bar
					miniplayer.drawBar(x, y, x_end, y_end, bar_width,frequency_array[i]);
					angle++;
				}
			}
			window.requestAnimationFrame(miniplayer.animation);
		},
		bars: function(){
			if(miniplayer.instance.hasClass('open')){
				miniplayer.colorControl();
				// set to the size of device
				canvas = miniplayer.instance.find('canvas')[0];
				//canvas.width = miniplayer.instance.find('canvas').width();
				//canvas.height = ;
				ctx = canvas.getContext("2d");
				ctx.globalCompositeOperation = 'source-over';
				ctx.fillStyle = '#000000';
				ctx.fillRect(0,0,canvas.width,canvas.height);
				
				// find the center of the window
				center_x = canvas.width / 2;
				center_y = canvas.height / 2;
				
				// Let's find out how tall we want our items to be
				var bar_ratio = 256 / canvas.height;
				
				if(bars > center_x){
					var use_bars = center_x;
				}else{
					var use_bars = bars;
				}
				
				//use_bars = Math.floor(use_bars / 4);
				
				var bar_width = Math.ceil(canvas.width / use_bars) - 1;
				
				
				// style the background
				
				analyser.getByteFrequencyData(frequency_array);
				
				var gradient = ctx.createLinearGradient(0,0,0,canvas.height);
				
				var level = 0;
				
				var bar_increment = ~~(miniplayer.binCount / bars);
				
				var use_increment = ~~(miniplayer.binCount / use_bars);
				
				for(var i = 0; i < miniplayer.binCount; i += bar_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					level += frequency_array[i];
				}
				level = Math.floor((level / bars) / 2);
				
				radius = level;
				var comp = miniplayer.getCompliment(miniplayer.curColor);
				var lvl_temp = level / 255;
				
				gradient.addColorStop(0,"rgba("+(comp.r * lvl_temp)+","+(comp.g * lvl_temp)+", "+(comp.b * lvl_temp)+", 1)");
				gradient.addColorStop(1,"rgba(0, 0, 0, 1)");
				ctx.fillStyle = gradient;
				ctx.fillRect(0,0,canvas.width,canvas.height);
				ctx.fillStyle = 'transparent';
				
				var cur_x = 0;
				
				for(var i = 0; i < miniplayer.binCount; i += use_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					//divide a circle into equal parts
					//rads = Math.PI * 2 / bars;
					
					bar_height = frequency_array[i] / bar_ratio;
					
					// set coordinates
					x = cur_x;
					y = canvas.height;
					x_end = cur_x;
					y_end = canvas.height - bar_height;
					
					//draw a bar
					miniplayer.drawBar(x, y, x_end, y_end, bar_width,frequency_array[i]);
					
					cur_x += bar_width + 1;
				
				}
			}
			window.requestAnimationFrame(miniplayer.animation);
		},
		spectro: function(){
			if(miniplayer.instance.hasClass('open')){
				miniplayer.colorControl();
				// set to the size of device
				canvas = miniplayer.instance.find('canvas')[0];
				//canvas.width = miniplayer.instance.find('canvas').width();
				//canvas.height = ;
				ctx = canvas.getContext("2d");
				ctx.globalCompositeOperation = 'source-over';
				ctx.fillStyle = '#000000';
				ctx.fillRect(0,0,canvas.width,canvas.height);
				
				// find the center of the window
				center_x = canvas.width / 2;
				center_y = canvas.height / 2;
				
				// Let's find out how tall we want our items to be
				var bar_ratio = 256 / canvas.height;
				
				if(bars > center_x){
					var use_bars = center_x;
				}else{
					var use_bars = bars;
				}
				
				//use_bars = Math.floor(use_bars / 4);
				
				var line_width = 2;
				var bar_width = Math.ceil(canvas.width / use_bars) - 1;
				
				
				// style the background
				
				analyser.getByteFrequencyData(frequency_array);
				
				var gradient = ctx.createLinearGradient(0,0,0,canvas.height);
				
				var level = 0;
				
				var bar_increment = ~~(miniplayer.binCount / bars);
				
				var use_increment = ~~(miniplayer.binCount / use_bars);
				
				for(var i = 0; i < miniplayer.binCount; i += bar_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					level += frequency_array[i];
				}
				level = Math.floor((level / bars) / 2);
				
				radius = level;
				
				var comp = miniplayer.getCompliment(miniplayer.curColor);
				var lvl_temp = level / 255;
				
				gradient.addColorStop(0,"rgba("+(comp.r * lvl_temp)+","+(comp.g * lvl_temp)+", "+(comp.b * lvl_temp)+", 1)");
				gradient.addColorStop(1,"rgba(0, 0, 0, 1)");
				ctx.fillStyle = gradient;
				ctx.fillRect(0,0,canvas.width,canvas.height);
				ctx.fillStyle = 'transparent';
				
				var cur_x = 0;
				
				for(var i = 0; i < miniplayer.binCount; i += use_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					//divide a circle into equal parts
					//rads = Math.PI * 2 / bars;
					if(i + use_increment < miniplayer.binCount){
						bar_height = frequency_array[i] / bar_ratio;
						bar_height2 = frequency_array[i + use_increment] / bar_ratio;
						
						// set coordinates
						x = cur_x;
						y = canvas.height - bar_height;
						x_end = cur_x + bar_width + 1;
						y_end = canvas.height - bar_height2;
						
						//draw a bar
						miniplayer.drawBarSolid(x, y, x_end, y_end, line_width,frequency_array[i]);
						
						cur_x += bar_width + 1;
					}
				}
			}
			window.requestAnimationFrame(miniplayer.animation);
		},
		driving: function(){
			if(miniplayer.instance.hasClass('open')){
				//Color shortcuts
				var sunset_orange = {
					r: 253,
					g: 94,
					b: 83
				};
				var sky_blue = {
					r: 83,
					g: 241,
					b: 252
				};
				var evening_purple = {
					r: 75,
					g: 0,
					b: 130
				};
				var rainy_gray = {
					r: 69,
					g: 69,
					b: 69
				};
				var sunrise_pink = {
					r: 251,
					g: 114,
					b: 152
				};
				
				miniplayer.colorControl();
				// set to the size of device
				canvas = miniplayer.instance.find('canvas')[0];
				//canvas.width = miniplayer.instance.find('canvas').width();
				//canvas.height = ;
				ctx = canvas.getContext("2d");
				ctx.globalCompositeOperation = 'source-over';
				
				// find the center of the window
				center_x = canvas.width / 2;
				center_y = canvas.height / 2;
				
				var bar_ratio = 256 / canvas.height;
				
				if(bars > center_x){
					var use_bars = center_x;
				}else{
					var use_bars = bars;
				}
				
				analyser.getByteFrequencyData(frequency_array);
				var level = 0;
				
				var bar_increment = ~~(miniplayer.binCount / bars);
				
				for(var i = 0; i < miniplayer.binCount; i += bar_increment){
					if(i >= miniplayer.binCount){
						i = miniplayer.binCount - 1;
					}
					level += frequency_array[i];
				}
				level = Math.floor((level / bars) / 4);
				
				var number_lines = 100;
				
				var scale_y = ~~(center_y / number_lines);
				var scale_x = ~~(center_x / 2);
				for( var i = 0; i < number_lines; i++ ){
					miniplayer.drawRoad(i, level, scale_x, scale_y, center_x, center_y);
				}
				
			}
			window.requestAnimationFrame(miniplayer.animation);
		},
		error: function(e){
			var string = 'There has been an issue loading: ' + miniplayer.instance.find('.mini-player-label.title').html() + '<br><br>(Line ' + e.lineno + '): ' + e.message;
			var w = aPipup.newWindow(string);
		},
		init: function(){
			
			miniplayer.chooseColor();
			miniplayer.curColor = miniplayer.trueColor;
			miniplayer.instance = $(miniplayer.seed).appendTo('body');
			miniplayer.audio = $('.mini-player .current-track');
			miniplayer.src = $('.mini-player .current-track .current-source');
			miniplayer.header = $('.mini-player .mini-player-header .title');
			
			miniplayer.animation = miniplayer.<?php if(!empty($_SESSION['def_visual'])){ echo $_SESSION['def_visual']; }else{ echo 'cleanCircle'; } ?>;
	
			context = new (window.AudioContext || window.webkitAudioContext)();
			analyser = context.createAnalyser();
			
			source = context.createMediaElementSource(miniplayer.audio[0]);
			source.connect(analyser);
			analyser.connect(context.destination);
			
			frequency_array = new Uint8Array(analyser.frequencyBinCount);
			miniplayer.binCount = ~~(analyser.frequencyBinCount * miniplayer.binPercent);
			
			if(window.localStorage.getItem('vizualizer') != undefined){
				miniplayer.animation = miniplayer[window.localStorage.getItem('vizualizer')];
			}
			
			window['miniplayer']['animation'](); //Variable variables in JavaScript the jank way.
			
			miniplayer.instance.find('.favorite').click(function(){
				miniplayer.toggleFavorite();
			});
			
			$('.mini-player .sleep-timer').click(function(){
				
				var string = '<h3>Sleep Timer</h3>';
				if(miniplayer.sleep_timer != false){
					string += '<button id="clear-current"><i class="fa fa-times"></i> Clear Current Timer</button><br><br>';
				}
				string += '<input class="auto" type="number" id="hours" value="0" min="0">:<input class="auto" type="number" id="minutes" value="0" min="0" max="59"><br><button id="start"><i class="fa fa-clock-o"></i> Start Timer</button>';
				
				var p = aPopup.newWindow(string);
				p.find('#start').click(function(){
					var hours = Number(p.find('#hours').val());
					var minutes = Number(p.find('#minutes').val());
					var temp = (hours * 60) + minutes;
					var seconds = (temp * 60) * 1000;
					//JavaScript seconds are not exact, but they are close enough for this.
					miniplayer.sleep_timer = setTimeout(function(){
						miniplayer.audio[0].pause();
						miniplayer.sleep_timer = false;
					}, seconds);
					p.remove();
				});
				p.find('#clear-current').click(function(){
					clearTimeout(miniplayer.sleep_timer);
					miniplayer.sleep_timer = false;
					$(this).remove();
				});
			});
			
			$('.mini-player .fa.toggle').click(function(){ miniplayer.toggle()});
			$('.mini-player .fa.close').click(function(){ miniplayer.remove()});
			$('.mini-player .fa.option').click(function(){ miniplayer.options()});
			$('.mini-player .fa.fullscreen').click(function(){ miniplayer.fullscreen()});
			$('.mini-player .playlist-toggle').click(function(){
				if($('.mini-player .mini-player-playlist').hasClass('open')){
					$('.mini-player .mini-player-playlist').removeClass('open');
				}else{
					$('.mini-player .mini-player-playlist').addClass('open');
				}
			});
			//This needs to be rewritten to be audio DOM event driven rather than click event driven.
			$('.mini-player-audio-controls .mini-player-play').click(function(){
				if(miniplayer.src.prop('src') != ''){
					if($(this).hasClass('fa-play')){
						miniplayer.audio[0].play();
					}else{
						miniplayer.audio[0].pause();
					}
				}
			});
			
			$('.mini-player-audio-controls .seek').change(function(){
				miniplayer.audio[0].currentTime = $(this).val();
			});
			miniplayer.audio.on('loadedmetadata', function(){
				$('.seek').attr('max', miniplayer.audio[0].duration);
			});
			miniplayer.audio[0].addEventListener('timeupdate', function(){
				var curtime = parseInt(miniplayer.audio[0].currentTime, 10);
				$('.mini-player-audio-controls .seek').val(curtime);
				var friendly = miniplayer.timeFormat(miniplayer.audio[0].currentTime);
				miniplayer.instance.find('.mini-player-counter').html(friendly + ' / ' + miniplayer.fDur);
				if('mediaSession' in navigator && 'MediaPositionState' in navigator.mediaSession){
					navigator.mediaSession.setPositionState({
						duration: miniplayer.audio[0].duration,
						playbackRate: 1,
						position: miniplayer.audio[0].currentTime
					});
				}
				if(playlist.playing == true && playlist.list[playlist.i + 1] != undefined){
					var check = (miniplayer.audio[0].currentTime / miniplayer.audio[0].duration);
					if(check >= 0.5 && miniplayer.next_id == false){
						miniplayer.preload(playlist.list[playlist.i + 1]);
					}
				}
			});
			miniplayer.audio[0].onended = function(){
				if(playlist.playing == false){
					$('.mini-player-audio-controls .mini-player-play').removeClass('fa-pause').addClass('fa-play');
					miniplayer.audio[0].currentTime = 0.0;
				}else{
					playlist.next();
				}
			}
			
			$('.mini-player-shuffle').click(function(){
				if(playlist.playing == true){
					if(playlist.shuffle == true){
						$(this).removeClass('active');
						playlist.shuffle = false;
					}else{
						$(this).addClass('active');
						playlist.shuffle = true;
					}
				}
			});
			
			$('.mini-player-loop').click(function(){
				if(playlist.playing == true){
					switch(playlist.loopMode){
						case 'loop':
							playlist.loopMode = 'one';
							$(this).addClass('one');
							break;
						case 'one':
							playlist.loopMode = 'none';
							$(this).removeClass('active').removeClass('one');
							break;
						case 'none':
							playlist.loopMode = 'loop';
							$(this).addClass('active');
					}
				}
			});
			
			$('.mini-player-prev').click(function(){
				if(playlist.playing == true){
					playlist.prev();
				}
			});
			$('.mini-player-next').click(function(){
				if(playlist.playing == true){
					playlist.next();
				}
			});
			
			if('mediaSession' in navigator){
				navigator.mediaSession.setActionHandler('play', function(){
					miniplayer.audio[0].play();
				});
				navigator.mediaSession.setActionHandler('pause', function(){
					miniplayer.audio[0].pause();
				});
				navigator.mediaSession.setActionHandler('nexttrack', function(){
					playlist.next();
				});
				navigator.mediaSession.setActionHandler('previoustrack', function(){
					playlist.prev();
				});
			}else{
			
				/*
				Name: Stephen Kennedy
				Date: 8/5/2020
				Comment: This works when in focus, but we don't need to use it if we use the mediaSession API, so it has been moved here as a fallback if we don't have access to the API.
				*/
				$(document).on('keydown', function(e){
					switch(e.originalEvent.code){
						case 'MediaPlayPause':
								if(miniplayer.src.prop('src') != ''){
									if($('.mini-player-audio-controls .mini-player-play').hasClass('fa-play')){
										miniplayer.audio[0].play();
									}else{
										miniplayer.audio[0].pause();
									}
								}
							break;
						case 'MediaStop':
								miniplayer.audio[0].pause();
								miniplayer.audio[0].currentTime = 0.0;
							break;
						case 'MediaTrackPrevious':
							console.log('Previous');
							if(playlist.playing == true){
								playlist.prev();
							}
							break;
						case 'MediaTrackNext':
							console.log('Next');
							if(playlist.playing == true){
								playlist.next();
							}						
							break;
					}
				});
			}
			
			//Update these functions with the class changes when you come back to this.
			miniplayer.audio.on('play', function(){
				miniplayer.playing = true;
				clearTimeout(miniplayer.h_loop);
				miniplayer.h_loop = setTimeout(miniplayer.updateHistory, miniplayer.h_freq);
				$('.mini-player-audio-controls .mini-player-play').removeClass('fa-play').addClass('fa-pause');
			});
			
			miniplayer.audio.on('pause', function(){
				miniplayer.playing = false;
				clearTimeout(miniplayer.h_loop);
				//Because the first thing I do before changing devices is pause
				miniplayer.updateHistory();
				$('.mini-player-audio-controls .mini-player-play').removeClass('fa-pause').addClass('fa-play');
			});
			$('.mini-player-audio-controls .seek').mousemove(function(e){
				var seek = $(this);
				var span = $('.mini-player-seek-counter');
				
				/*
				Name: Stephen Kennedy
				Date: 2/22/21
				Comment: Now we do math to see what percentage we are in the bar.
				*/
				var bar_width = seek.width();
				var mouse_pos = e.pageX - seek.offset().left;
				var percent = mouse_pos / bar_width;
				var timecode = percent * miniplayer.audio[0].duration;
				timecode = miniplayer.timeFormat(timecode);
				span.html(timecode);
				
				
				var y = ((seek.offset().top - $(window).scrollTop()) * -1) + window.innerHeight;
				var x = e.pageX - (span.width() / 2);
				if(span.hasClass('hidden')){
					span.removeClass('hidden');
				}
				span.attr('style', 'left:'+x+'px;bottom:'+y+'px;');
			});
			$('.mini-player-audio-controls .seek').mouseleave(function(){
				$('.mini-player-seek-counter').addClass('hidden');
			});
			
			
			
		}
	};
	miniplayer.init();
	$('.miniplayer-play').click(function(){
		playlist.playing = false;
		playlist.disable();
		var id = $(this).data('id');
		miniplayer.load(id);
	});
	var canvas, ctx, center_x, center_y, radius, bars, 
    x_end, y_end, bar_height, bar_width,
    frequency_array, time_array, processor;
 
bars = 200;
bar_width = 2;
 
function initPage(){
    var context = '';
	var analyzer = '';
	
}

initPage();
});

var playlist = {
	list: [],
	i: 0,
	pending: 0,
	pTimeout: false,
	loopMode: 'none',
	playing: false,
	window: false,
	currentChunk: false,
	shuffle: false,
	shuffleMode: "local",
	shuffleHistory: [],
	shuffleURL: '<?php echo build_slug("ajax/ajax_server_shuffle/audio"); ?>',
	favShuffleURL: '<?php echo build_slug('ajax/ajax_server_fav_shuffle/audio'); ?>',
};

playlist.serverShuffle = function(){
	//Set ourselves up.
	playlist.shuffleMode = 'server';
	playlist.list = [];
	playlist.shuffle = true;
	playlist.shuffleHistory = [];
	playlist.i = 0;
	
	var search = $('#search').val();
	
	//Get our first chunk of 100 songs and start playback.
	$.post(playlist.shuffleURL, {search: search}, function(content){
		if(content.error === false){
			playlist.list = content.chunk;
			playlist.play(0);
			var html = $('.miniplayer-server-shuffle').html();
			html = html.replace(' ... <i class="fa fa-fw fa-cog fa-spin"></i>', '');
			$('.miniplayer-server-shuffle').html(html);
			$('.miniplayer-server-shuffle').prop('disabled', false);
		}else{
			console.log(content.message);
		}
	});
}

playlist.serverFavShuffle = function(){
	//Set ourselves up.
	playlist.shuffleMode = 'server';
	playlist.list = [];
	playlist.shuffle = true;
	playlist.shuffleHistory = [];
	playlist.i = 0;
	
	var search = $('#search').val();
	
	//Get our first chunk of 100 songs and start playback.
	$.post(playlist.favShuffleURL, {search: search}, function(content){
		if(content.error === false){
			playlist.list = content.chunk;
			playlist.play(0);
			var html = $('.miniplayer-server-fav-shuffle').html();
			html = html.replace(' ... <i class="fa fa-fw fa-cog fa-spin"></i>', '');
			$('.miniplayer-server-fav-shuffle').html(html).prop('disabled', false);
		}else{
			console.log(content.message);
		}
	});
}

playlist.clear = function(){
	$('#current .controls .counter').addClass('hidden').html('');
	playlist.pending = 0;
}

playlist.push = function(){
	if($('#current .controls .counter').hasClass('hidden')){
		$('#current .controls .counter').removeClass('hidden');
	}
	playlist.pending++;
	$('#current .controls .counter').html('+' + playlist.pending);
	clearTimeout(playlist.pTimeout);
	playlist.pTimeout = setTimeout(playlist.clear, 5000);
}

playlist.bind = function(){
	$('.playlist-add').off().click(function(){
		playlist.list.push($(this).data('id'));
		playlist.push();
		playlist.render();
	});
	$('.playlist-track span').off().click(function(){
		var id = $(this).parent().data('id');
		playlist.play(id);
	});
	$('.playlist').off().sortable({
		handle: '.playlist-handle',
		deactivate: playlist.reorder
	});
	$('.playlist-track i.playlist-remove').off().click(function(){
		playlist.splice(this);
	});
	$('#current .playlist-save').off().click(function(){
		playlist.save();
	});
	$('#current .playlist-new').off().click(function(){
		playlist.list = [];
		playlist.i = 0;
		playlist.render();
		$('#current').find('h2').attr('data-id', '').attr('data-title', '');
		$('#current-playlist').html('');
	});
}

playlist.fetch = function(){
	$.get('<?php echo build_slug("ajax/ajax_get_playlists/audio"); ?>').done(function(returned){
		var string = '<ul>';
		for(i in returned){
			string += '<li class="playlist-list" data-id="' + returned[i].id + '" data-tracks="'+ returned[i].list +'"><span class="playlist-title">' + returned[i].title + '</span><i class="fa fa-times delete-playlist"></i></li>';
		}
		string += '</ul>';
		$('#playlists').html(string);
		$('#playlists .playlist-list .playlist-title').click(function(){
			playlist.list = $(this).parent().data('tracks');
			playlist.render();
			$('.tray').removeClass('open');
			$('#current').addClass('open');
			$('#current').find('h2').attr('data-title', $(this).html());
			$('#current').find('h2').attr('data-id', $(this).data('id'));
		});
		$('#playlists .playlist-list .delete-playlist').click(function(){
			var selected = $(this).parent();
			var playlist_id = selected.data('id');
			var playlist_title = selected.children('.playlist-title').html();
			var string = '<h2>Confirm Playlist Delete</h2> Please confirm that you would like to delete the playlist:<br>'+playlist_title+'<br><br><button data-id="'+playlist_id+'" class="yes"><i class="fa fa-check"></i> Yes</button> <button class="no"><i class="fa fa-times"></i> No</button>';
			
			var w = aPopup.newWindow(string);
			
			w.find('.yes').click(function(){
				var id = $(this).data('id');
				$.post('<?php echo build_slug("ajax/ajax_playlist_delete/audio"); ?>', {
					id: id
				}).done(function(){
					w.remove();
					playlist.fetch();
				});
			});
			
			w.find('.no').click(function(){
				w.remove();
			});
		});
	});
}

playlist.save = function(){
	var string = '<h2>Save the current playlist?</h2><br><label>Name</label>';
	
	var playlist2 = $('#now_playing h2#title');
	var id = playlist2.data('id');
	var title = playlist2.data('title');
	
	if((title == undefined && id == undefined) || (title == "" && id == "")){
		string += '<input type="text" id="playlist-name" placeholder="Playlist Name...">';
	}else{
		string += '<input type="text" id="playlist-name" placeholder="name" value="'+ title +'">';
		string += '<input type="hidden" id="playlist-id" value="'+ id +'">';
		string += '<br><input class="inline" type="checkbox" id="rename"> <label class="inline">Replace Existing Name?</label>';
	}
	string += '<br><br><button id="save"><i class="fa fa-floppy-o"></i> Save</button>';
	var w = aPopup.newWindow(string);
	
	w.find('#save').click(function(){
		$(this).off();
		$(this).find('i').removeClass('fa-floppy-o').addClass('fa-cog').addClass('fa-spin');
		
		if(w.find('#playlist-id').length > 0 && w.find('#rename').prop('checked') == true){
			var id = w.find('#playlist-id').val();
		}else{
			var id = false;
		}
		
		var title = w.find('#playlist-name').val();
		
		var list = [];
		$('.playlist .playlist-track').each(function(){
			var tId = $(this).data('db-id');
			list.push(tId);
		});
		
		console.log(title, id, list);
		
		$.post('<?php echo build_slug("ajax/ajax_playlist_save/audio"); ?>', {
			title: title,
			id: id,
			list: list
		}).done(function(){
			w.remove();
			playlist.fetch();
		});
	});
}

playlist.render = function(){
	if(playlist.list.length > 0){
		$.get('<?php echo build_slug("ajax/ajax_playlist/audio"); ?>', {'songs': playlist.list, 'current': playlist.i}, function(returned){
			$('#current-playlist').html(returned);
			playlist.bind();
		});
	}else{
		$('.mini-player-playlist').html('');
	}
}

playlist.splice = function(dom){
	var parent = $(dom).parent();
	var id = parent.data('id');
	var dbId = parent.data('db-id');
	var restart = false;
	if(id == playlist.i){
		//If we're removing the current song we need to pause
		miniplayer.audio[0].pause();
		restart = true;
	}
	if(playlist.i >= (playlist.length - 1)){
		//If moving forward would give us an invalid value we go to the beginning
		miniplayer.audio[0].pause();
		playlist.i = 0;
		restart = true;
	}
	playlist.list.splice(id, 1);
	playlist.render();
	if(restart == true){
		playlist.play(playlist.i);
	}
}

playlist.reorder = function(event, ui){
	var newList = [];
	$('.playlist .playlist-track').each(function(){
		var id = $(this).data('db-id');
		if(id != undefined){
			newList.push(id);
		}
	});
	playlist.list = newList;
	playlist.render();
}

playlist.play = function(id){
	playlist.playing = true;
	playlist.i = id;
	miniplayer.load(playlist.list[playlist.i]);
	playlist.enable();
	playlist.render();
}

playlist.enable = function(){
	$('.mini-player-prev').removeClass('disable');
	$('.mini-player-next').removeClass('disable');
	$('.mini-player-shuffle').removeClass('disable');
	$('.mini-player-loop').removeClass('disable');
}
playlist.disable = function(){
	$('.mini-player-prev').addClass('disable');
	$('.mini-player-next').addClass('disable');
	$('.mini-player-shuffle').addClass('disable');
	$('.mini-player-loop').addClass('disable');
}

playlist.next = function(){
	miniplayer.audio[0].pause();
	if(playlist.shuffle == false || playlist.shuffleMode == 'server'){
		if(playlist.i + 1 >= playlist.list.length && playlist.shuffleMode != 'server'){
			if(playlist.loopMode == 'loop'){
				playlist.i = 0;
				miniplayer.load(playlist.list[0]);
			}else if(playlist.loopMode == 'one'){
				miniplayer.load(playlist.list[playlist.i]);
			}
		}else if(playlist.i + 1 >= playlist.list.length && playlist.shuffleMode == 'server'){
			var search = $('#search').val();
			var data = {prev: playlist.list, search: search};
			playlist.i = 0;
			$.post(playlist.shuffleURL, data, function(content){
				if(content.error === false){
					playlist.list = content.chunk;
					playlist.play(0);
				}else{
					console.log(content.message);
				}
			});
		}else{
			playlist.i++;
			miniplayer.load(playlist.list[playlist.i]);
		}
	}else{
		var next = playlist.ranArray(playlist.list);
		playlist.shuffleHistory.push(next);
		playlist.i = playlist.lastKey;
		if(playlist.shuffleHistory.length > 25){
			playlist.shuffleHistory.shift();
		}
		miniplayer.load(next);
	}
	playlist.render();
}

playlist.actual_prev = function(){
	if(playlist.shuffle == false || playlist.shuffleMode == 'server'){
		if(playlist.i - 1 < 0){
			if(playlist.loopMode == 'loop'){
				playlist.i = playlist.list.length - 1;
				miniplayer.load(playlist.list[playlist.i]);
			}else if(playlist.loopMode == 'one'){
				miniplayer.load(playlist.list[playlist.i]);
			}
		}else{
			playlist.i--;
			miniplayer.load(playlist.list[playlist.i]);
		}
	}else{
		var next = playlist.shuffleHistory.pop();
		if(next == undefined){
			next = playlist.ranArray(playlist.list);
			playlist.i = playlist.lastKey;
		}else{
			playlist.i = playlist.list.findIndex(function(item){
				return item == next;
			});
		}
		miniplayer.load(next);
	}
	playlist.render();
}
playlist.prev = function(){
	//Make it act like a real media player, and let you rewind the song before going to the previous one.
	if(miniplayer.audio[0].currentTime > 10){
		miniplayer.audio[0].currentTime = 0;
	}else{
		playlist.actual_prev();
	}
}
playlist.lastKey = 0;
playlist.ranArray = function(array, returnArray){
	if(returnArray === undefined){
		returnArray = false;
	}
	try{
		var key = Math.floor(Math.random()*array.length);
		var result = array[key];
		if(typeof result == 'object' && result.length != undefined && returnArray == false){
			return playlist.ranArray(result);
		}else{
			playlist.lastKey = key;
			return result;
		}
	}catch(err){
		console.log(array);
		console.log(err);
		return false;
	}
}

playlist.edit = function(id){
	if(id == undefined){
		var track = '<li class="editor-item" data-id="{id}"><i class="fa fa-bars"></i> <span class="name">{name}</span> <span class="duration">{duration}</span> <i class="fa fa-times"></i></li>';
		var tracks = '<ul style="padding-top: 20px;padding-bottom:20px;" class="playlist-editor">';
		var trackInfo = $('.mini-player-playlist .playlist-track');
		for(i in playlist.list){
			var temp = $(trackInfo[i]);
			var id = playlist.list[i];
			var name = temp.find('.name').html();
			var duration = temp.find('.duration').html();
			tracks += track.replace('{id}', id).replace('{name}', name).replace('{duration}', duration);
		}
		tracks += '</ul>';
		
		var playlists = '<select id="playlists"><option value="false">[ New Playlist ]';
		if(localStorage.playlists != undefined){
			var tempLists = JSON.parse(localStorage.playlists);
			
		}else{
			var tempLists = [];
		}
		for(i in tempLists){
			playlists += '<option value="' + i + '">' + tempLists[i].name + '</option>';
		}
		playlists += '</select>';
		
		tracks = playlists + '<input type="hidden" name="id" id="id" value="false"><label for="playlist-name">Name</label><input id="playlist-name" type="text" placeholder="Playlist Name..." value="">' + tracks + '<button id="save-playlist"><i class="fa fa-floppy-o"></i> Save</button> <button id="clear-playlist"><i class="fa fa-times"></i> Delete</button>';
	}
	//var w = aPopup.newWindow(tracks);
	var w = $('#current-playlist');
	w.html(tracks);
	w.find('#playlists').change(function(){
		//Left off here. Need to create a function to load a saved playlist into the editor
	});
	w.find('li.editor-item .fa-times').click(function(){
		$(this).parent().remove();
	});
	w.find('.playlist-editor').sortable({
		handle: '.fa-bars'
	});
	w.find('#clear-playlist').click(function(){
		playlist.list = [];
		playlist.render();
		var playlistId = w.find('#id').val();
		if(playlistId != 'false'){
			var tempLists = JSON.parse(localStorage.playlists);
			tempLists.splice(playlistId, 1);
			localStorage.playlists = JSON.stringify(tempLists);
		}
		w.remove();
	});
	w.find('#save-playlist').click(function(){
		var ids = [];
		w.find('li.editor-item').each(function(){
			var id = $(this).data('id');
			ids.push(id);
		});
		playlist.list = ids;
		
		var name = w.find('#playlist-name').val();
		var playlistId = w.find('#id').val();
		var saveList = {
			name: name,
			items: ids,
			global: false
		}
		if(playlistId == 'false'){
			if(localStorage.playlists == undefined){
				var tempLists = [saveList];
			}else{
				var tempLists = JSON.parse(localStorage.playlists);
				tempLists.push(saveList);
			}
		}else{
			var tempLists = JSON.parse(localStorage.playlists);
			var oldList = tempLists[playlistId];
			if(oldList.global == true){
				saveList.global == true;
			}
			tempLists[playlistId] = saveList;
		}
		localStorage.playlists = JSON.stringify(tempLists);
		playlist.render();
		w.remove();
	});
}

Object.defineProperty(Array.prototype, 'shuffle', {
    value: function() {
        for (let i = this.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [this[i], this[j]] = [this[j], this[i]];
        }
        return this;
    }
});
</script>
