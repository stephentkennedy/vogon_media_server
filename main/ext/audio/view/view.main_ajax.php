<?php
	if(!empty($_GET['t'])){
		$type = $_GET['t'];
	}
?><header><h1>Audio Library</h1></header>
<style>#ajax-output{
	padding-top: 20px;
}</style>
<div>
	<label for="search">Search</label>
	<input type="text" id="search" placeholder="Search...">
	<button type="button" id="search-confirm">Search</button>
</div>
<div id="ajax-output">
	Loading Content...
</div>
<?php echo load_view('mini_player', [], 'audio'); ?>
<script type="text/javascript">
	var controller = {
		url: {
			'default': '<?php echo build_slug("ajax/ajax_search/audio"); ?>',
			'artists': '<?php echo build_slug("ajax/ajax_search_artist/audio"); ?>',
			'albums': '<?php echo build_slug("ajax/ajax_search_album/audio"); ?>',
			'genre': ''
		},
		active_url: '<?php switch($type){
							case 'artists':
								echo 'artists';
								break;
							case 'albums':
								echo 'albums';
								break;
							default: 
								echo 'default'; 
								break; 
						} ?>'
	};
	$(document).ready(function(){
		$('.nav.audio').removeAttr('href').attr('data-search', 'default');
		$('.subnav.audio').removeAttr('href');
		$('#main-nav a.audio').click(function(){
			var dom = $(this);
			var search = dom.data('search');
			controller.active_url = search;
			controller.page(1);
		});
		controller.load = function(content){
			$('#ajax-output').html(content);
			$('.page-change').click(function(){
				var page = $(this).data('page');
				controller.page(page);
			});
			$('.miniplayer-play').click(function(){
				playlist.playing = false;
				playlist.disable();
				var id = $(this).data('id');
				miniplayer.load(id);
			});
			$('.miniplayer-server-shuffle').click(function(){
				$(this).append(' ... <i class="fa fa-fw fa-cog fa-spin"></i>');
				$(this).prop('disabled', true);
				playlist.playing = false;
				playlist.currentChunk = false;
				playlist.serverShuffle();
			});
			$('.miniplayer-server-fav-shuffle').click(function(){
				$(this).append(' ... <i class="fa fa-fw fa-cog fa-spin"></i>');
				$(this).prop('disabled', true);
				playlist.playing = false;
				playlist.currentChunk = false;
				playlist.serverFavShuffle();
			});
			$('.ajax-form').off().click(function(){
				if($(this).data('href') != ''){
					var data = {
						format: 'ajax_form'
					};
					$.get($(this).data('href'), data, function( returned ){
						app.ajax_form(returned);
					});
				}
			});
			playlist.bind();
		}
		controller.page = function(page, push){
			if(push == undefined){
				push = true;
			}
			var data = {
				'format': 'HTML',
				'page': page,
				'type': controller.active_url
			};
			var search = $('#search').val();
			var url = '?p=' + page;
			if(search != ''){
				data['search'] = search;
				url += '&s=' + encodeURI(search);
			}
			url += '&t=' + encodeURI(controller.active_url);
			controller.load('<i class="fa fa-cog fa-spin"></i>');
			if(push == true){
				history.pushState(data, '', url);
			}
			$.get(controller.url[controller.active_url], data , function(content){
				controller.load(content);
			});
		}
		$('#search-confirm').click(function(){
			var search = $('#search').val();
			controller.page(1);
		});
		$('#search').keypress(function(e){
			if( e.which == 13){
				var search = $('#search').val();
				controller.page(1);
			}
		});
		
		//This function tells the browser what to do if back is pushed for one of our states
		window.onpopstate = function(e){
			if(e.state != null){
				var state = e.state;
				if(state.search != undefined && state.search != ''){
					$('#search').val(state.search);
				}else{
					$('#search').val('');
				}
				if(state.type != undefined && state.type != ''){
					controller.active_url = state.type;
				}
				controller.page(state.page, false);
			}else{
				var url = new URL(window.location);
				var s = url.search;
				if(s.length == 0){
					//No search, no page
					controller.page(1, false);
				}else{
					//Split our search into sections
					s = s.split('&');
					//Trim the leading ? off the first entry
					s[0] = s[0].replace('?', '');
					var params = {};
					for(var i = 0; i < s.length; i++){
						var temp = s[i].split('=');
						params[temp[0]] = decodeURI(temp[1]);
					}
					if(params['s'] != undefined){
						$('#search').val(params['s']);
					}else{
						$('#search').val('');
					}
					if(params['t'] != undefined){
						controller.active_url = params['t'];
					}
					if(params['p'] != undefined){
						var page = params['p'];
					}else{
						var page = 1;
					}
					controller.page(page, false);
				}
			}
			//Change page without pushing state
		}
		
		var url = new URL(window.location);
		var s = url.search;
		if(s.length == 0){
			//No search, no page
			controller.page(1, false);
		}else{
			//Split our search into sections
			s = s.split('&');
			//Trim the leading ? off the first entry
			s[0] = s[0].replace('?', '');
			var params = {};
			for(var i = 0; i < s.length; i++){
				var temp = s[i].split('=');
				params[temp[0]] = decodeURI(temp[1]);
			}
			if(params['s'] != undefined){
				$('#search').val(params['s']);
			}
			if(params['p'] != undefined){
				var page = params['p'];
			}else{
				var page = 1;
			}
			controller.page(page, false);
		}
	});
	
</script>
