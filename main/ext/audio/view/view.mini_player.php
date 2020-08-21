<script type="text/javascript">
var miniplayer = {
};
$(document).ready(function(){
	playlist.bind();
	miniplayer = {
		instance: {},
		audio: {},
		id: {},
		src: {},
		header: {},
		angle: 0,
		track: false,
		playing: false,
		h_loop: false,
		h_freq: 10000,
		sleep_timer: false,
		animation: miniplayer.cleanCircle,
		seed: '<div class="mini-player"><header class="mini-player-header"><span class="shadow"></span><span class="title">Mini-Player</span><span class="controls"><i class="fa fa-window-maximize toggle"></i><i class="fa fa-cog option"></i><i class="fa fa-expand fullscreen"></i><i class="fa fa-times close"></i></span></header><canvas></canvas><audio class="current-track"><source class="current-source"></source></audio><div class="mini-player-audio-controls"><i class="fa fa-step-backward fa-fw  mini-player-prev disable"></i><i class="fa fa-play fa-fw  mini-player-play"></i><i class="fa fa-step-forward fa-fw  mini-player-next disable"></i><i class="fa fa-random  fa-fw mini-player-shuffle disable"></i><i class="fa fa-retweet fa-fw  mini-player-loop disable"></i><span class="mini-one">1</span><input type="range" class="seek" value="0" max="" /><i class="fa fa-fw fa-volume-up mini-player-volume"></i></div><i class="fa fa-list playlist-toggle"></i><i class="fa fa-clock-o sleep-timer"></i><div class="mini-player-playlist"></div></div>',
		load: function(id){
			if(miniplayer.instance == false){
				miniplayer.init();
			}
			$.get('<?php echo URI; ?>/ajax/ajax_audio/audio/' + id, function( returned ){
				data = returned;
				miniplayer.audio[0].pause();
				miniplayer.src.prop('src', data['src']);
				miniplayer.src.prop('type', data['mime']);
				miniplayer.id = data['id'];
				miniplayer.header.html('<span>'+data['title']+'</span>');
				if(miniplayer.header.find('span').width() > miniplayer.header.width()){
					miniplayer.header.find('span').addClass('marquee');
				}
				miniplayer.audio[0].load();
				if(data['time'] == undefined || data['time'] == 0){
					miniplayer.currentTime = 0.0;
					miniplayer.audio[0].currentTime = 0.0;
				}else{
					var min = 60;
					if(data['time'] <= (data['duration'] - .5 * min)){
						miniplayer.currentTime = data['time'];
						miniplayer.audio[0].currentTime = data['time'];
					}else{
						miniplayer.currentTime = 0.0;
						miniplayer.audio[0].currentTime = 0.0;
					}					
				}
				if(data['time'] == undefined){
					miniplayer.track = false;
				}else{
					miniplayer.track = true;
				}
				miniplayer.audio[0].play();
				if('mediaSession' in navigator){
					navigator.mediaSession.metadata = new MediaMetadata({
						title: data['title'],
						artist: data['artist'],
						album: data['album']
					});
					if('MediaPositionState' in navigator.mediaSession){
						navigator.mediaSession.MediaPositionState.duration = data['length'];
						navigator.mediaSession.MediaPositionState.position = 0.0;
					}
				}
			});			
		},
		init: function(){
			miniplayer.instance = $(miniplayer.seed).appendTo('body');
			miniplayer.audio = $('.mini-player .current-track');
			miniplayer.src = $('.mini-player .current-track .current-source');
			miniplayer.header = $('.mini-player .mini-player-header .title');
			
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
				if('mediaSession' in navigator && 'MediaPositionState' in navigator.mediaSession){
					navigator.mediaSession.MediaPositionState.position = miniplayer.audio[0].currentTime;
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
				navigator.mediaSession.setActionHandler('play', miniplayer.play);
				navigator.mediaSession.setActionHandler('pause', miniplayer.play);
				navigator.mediaSession.setActionHandler('nexttrack', playlist.next);
				navigator.mediaSession.setActionHandler('previoustrack', playlist.prev);
			}
			
			/*
			Name: Stephen Kennedy
			Date: 8/5/2020
			Comment: This works when in focus, but we may not need to use it if we use the mediaSession API.
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
				$.get('/ajax/ajax_save_history/media', data, function(content){
					if(content == 'saved' && miniplayer.playing == true){
						miniplayer.h_loop = setTimeout(miniplayer.updateHistory, miniplayer.h_freq);
					}else if (content != 'saved'){
						alert(content);
					}
				});
			}
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
    
	miniplayer.animation = miniplayer.cleanCircle;
	
    context = new (window.AudioContext || window.webkitAudioContext)();
    analyser = context.createAnalyser();
    
    source = context.createMediaElementSource(miniplayer.audio[0]);
    source.connect(analyser);
    analyser.connect(context.destination);
    
    frequency_array = new Uint8Array(analyser.frequencyBinCount);

    window['miniplayer']['animation'](); //Variable variables in JavaScript the jank way.
}

miniplayer.cleanCircle = function(){
	if(miniplayer.instance.hasClass('open')){
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
		for(var i = 0; i < bars; i++){
			level += frequency_array[i];
		}
		level = Math.floor((level / bars) / 4);
		
		radius = level;
		
		gradient.addColorStop(0,"rgba("+level+", 7, "+level+", 1)");
		gradient.addColorStop(1,"rgba(0, 0, 0, 1)");
		ctx.fillStyle = gradient;
		ctx.fillRect(0,0,canvas.width,canvas.height);
		ctx.fillStyle = 'transparent';
		
		//draw a circle
		//ctx.strokeStyle = 'rgba(150,0,100,1)';
		ctx.beginPath();
		ctx.arc(center_x,center_y,radius,0,2*Math.PI);
		ctx.stroke();
		for(var i = 0; i < bars; i++){
			
			//divide a circle into equal parts
			rads = Math.PI * 2 / bars;
			
			bar_height = frequency_array[i]*0.7 + (150 - level);
			
			// set coordinates
			x = center_x + Math.cos(rads * i) * (radius);
		y = center_y + Math.sin(rads * i) * (radius);
			x_end = center_x + Math.cos(rads * i)*(radius + bar_height);
			y_end = center_y + Math.sin(rads * i)*(radius + bar_height);
			
			//draw a bar
			miniplayer.drawBar(x, y, x_end, y_end, bar_width,frequency_array[i]);
		
		}
	}
	window.requestAnimationFrame(miniplayer.animation);
}

miniplayer.burnout = function(){
    if(miniplayer.instance.hasClass('open')){
		// set to the size of device
		canvas = miniplayer.instance.find('canvas')[0];
		//canvas.width = miniplayer.instance.find('canvas').width();
		//canvas.height = ;
		ctx = canvas.getContext("2d");
		ctx.globalCompositeOperation = 'hard-light';
		
		// find the center of the window
		center_x = canvas.width / 2;
		center_y = canvas.height / 2;
		
		
		// style the background
		
		analyser.getByteFrequencyData(frequency_array);
		
		var gradient = ctx.createLinearGradient(0,0,0,canvas.height);
		
		var level = 0;
		for(var i = 0; i < bars; i++){
			level += frequency_array[i];
		}
		level = Math.floor((level / bars) / 4);
		
		radius = level;
		
		var temp_width = canvas.width - 4;
		var temp_height = canvas.height - 4;
		
		ctx.translate(center_x, center_y);
		miniplayer.angle += 2;
		if(miniplayer.angle > 360){
			miniplayer.angle = 0;
		}
		var radian = miniplayer.angle * Math.PI / 180;
		//ctx.rotate(radian);
		ctx.drawImage(canvas, Math.floor(temp_width / 2) * -1, Math.floor(temp_height / 2) * -1, temp_width, temp_height);
		ctx.globalCompositeOperation = 'hard-light';
		//ctx.rotate(-1 * radian);
		ctx.setTransform(1, 0, 0, 1, 0, 0);
		
		gradient.addColorStop(0,"rgba("+level+", 7, "+level+", 1)");
		gradient.addColorStop(1,"rgba(0, 0, 0, 0)");
		ctx.fillStyle = gradient;
		ctx.fillRect(0,0,canvas.width,canvas.height);
		ctx.fillStyle = 'transparent';
		
		//draw a circle
		ctx.strokeStyle = 'rgba(150,0,100,1)';
		ctx.beginPath();
		ctx.arc(center_x,center_y,radius,0,2*Math.PI);
		ctx.stroke();
		for(var i = 0; i < bars; i++){
			
			//divide a circle into equal parts
			rads = Math.PI * 2 / bars;
			
			bar_height = frequency_array[i]*0.7 + (150 - level);
			
			// set coordinates
			x = center_x + Math.cos(rads * i) * (radius);
		y = center_y + Math.sin(rads * i) * (radius);
			x_end = center_x + Math.cos(rads * i)*(radius + bar_height);
			y_end = center_y + Math.sin(rads * i)*(radius + bar_height);
			
			//draw a bar
			miniplayer.drawBar(x, y, x_end, y_end, bar_width,frequency_array[i]);
		
		}
	}
	window.requestAnimationFrame(miniplayer.animation);
}
 
miniplayer.bars = function(){
	if(miniplayer.instance.hasClass('open')){
		// set to the size of device
		canvas = miniplayer.instance.find('canvas')[0];
		//canvas.width = miniplayer.instance.find('canvas').width();
		//canvas.height = ;
		ctx = canvas.getContext("2d");
		ctx.globalCompositeOperation = 'source-over';
		
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
		for(var i = 0; i < use_bars; i++){
			level += frequency_array[i];
		}
		level = Math.floor((level / bars) / 4);
		
		radius = level;
		
		gradient.addColorStop(0,"rgba("+level+", 7, "+level+", 1)");
		gradient.addColorStop(1,"rgba(0, 0, 0, 1)");
		ctx.fillStyle = gradient;
		ctx.fillRect(0,0,canvas.width,canvas.height);
		ctx.fillStyle = 'transparent';
		
		var cur_x = 0;
		
		for(var i = 0; i < use_bars; i++){
			
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
}

miniplayer.spectro = function(){
	if(miniplayer.instance.hasClass('open')){
		// set to the size of device
		canvas = miniplayer.instance.find('canvas')[0];
		//canvas.width = miniplayer.instance.find('canvas').width();
		//canvas.height = ;
		ctx = canvas.getContext("2d");
		ctx.globalCompositeOperation = 'source-over';
		
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
		for(var i = 0; i < use_bars; i++){
			level += frequency_array[i];
		}
		level = Math.floor((level / bars) / 4);
		
		radius = level;
		
		gradient.addColorStop(0,"rgba("+level+", 7, "+level+", 1)");
		gradient.addColorStop(1,"rgba(0, 0, 0, 1)");
		ctx.fillStyle = gradient;
		ctx.fillRect(0,0,canvas.width,canvas.height);
		ctx.fillStyle = 'transparent';
		
		var cur_x = 0;
		
		for(var i = 0; i < use_bars; i++){
			
			//divide a circle into equal parts
			//rads = Math.PI * 2 / bars;
			if(i + 1 < use_bars){
				bar_height = frequency_array[i] / bar_ratio;
				bar_height2 = frequency_array[i + 1] / bar_ratio;
				
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
}

// for drawing a bar
miniplayer.drawBar = function (x1, y1, x2, y2, width,frequency){
    
    var lineColor = "rgb(" + frequency + ", " + frequency + ", " + 0 + ")";
    
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = width;
    ctx.beginPath();
    ctx.moveTo(x1,y1);
    ctx.lineTo(x2,y2);
    ctx.stroke();
}

miniplayer.drawBarSolid = function (x1, y1, x2, y2, width,frequency){
    
    var lineColor = "rgb(200,200,0)";
    
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = width;
    ctx.beginPath();
    ctx.moveTo(x1,y1);
    ctx.lineTo(x2,y2);
    ctx.stroke();
}

miniplayer.options = function(){
	var win = aPopup.newWindow('<label for="visualizer">Visualization</label><select id="visualizer"><option value="spectro">Spectrograph</option><option value="bars">Bars</option><option value="circle">Circle</option><!--option value="burnout">Burn In</option--></select><br><button class="confirm"><i class="fa fa-floppy-o"></i> Save</button>');
	win.find('.confirm').click(function(){
		var anim = win.find('#visualizer').val();
		switch(anim){
			case 'spectro':
				miniplayer.animation = miniplayer.spectro;
				break;
			case 'bars':
				miniplayer.animation = miniplayer.bars;
				break;
			case 'circle':
				miniplayer.animation = miniplayer.cleanCircle;
				break;
			case 'burnout':
				miniplayer.animation = miniplayer.burnout;
				break;
		}
	});
}

miniplayer.fullscreen = function(){
	var canvas = miniplayer.instance.find('canvas')[0];
	if(miniplayer.instance.hasClass('fullscreen')){
		miniplayer.instance.removeClass('fullscreen');
		var width = miniplayer.instance.width();
		canvas.width = width;
		canvas.height = 150;
	}else{
		miniplayer.instance.addClass('fullscreen');
		var width = miniplayer.instance.width();
		var height = miniplayer.instance.height() - 80;
		canvas.width = width;
		canvas.height = height;
	}
}

initPage();
});

var playlist = {
	list: [],
	i: 0,
	loopMode: 'none',
	playing: false,
	window: false,
	currentChunk: false,
	shuffle: false,
	shuffleMode: "local",
	shuffleHistory: [],
	shuffleURL: '/ajax/ajax_server_shuffle/audio'
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
		}else{
			console.log(content.message);
		}
	});
}

playlist.bind = function(){
	$('.playlist-add').off().click(function(){
		playlist.list.push($(this).data('id'));
		playlist.render();
	});
	$('.playlist-track').off().click(function(){
		var id = $(this).data('id');
		playlist.play(id);
	});
}

playlist.render = function(){
	if(playlist.list.length > 0){
		$.get('/ajax/ajax_playlist/audio', {'songs': playlist.list, 'current': playlist.i}, function(returned){
			$('.mini-player-playlist').html(returned);
			playlist.bind();
		});
	}else{
		$('.mini-player-playlist').html('');
	}
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
	var w = aPopup.newWindow(tracks);
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