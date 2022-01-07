<header>Video Library <a class="button" href="<?php echo build_slug('edit', [], 'media'); ?>">Add</a></header>
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
<script type="text/javascript">
	var controller = {};
	$(document).ready(function(){
		controller.load = function(content){
			$('#ajax-output').html(content);
			$('.page-change').click(function(){
				var page = $(this).data('page');
				controller.page(page);
			});
		}
		controller.page = function(page, push){
			if(push == undefined){
				push = true;
			}
			var data = {
				'format': 'HTML',
				'page': page
			};
			var search = $('#search').val();
			var url = '?p=' + page;
			if(search != ''){
				data['search'] = search;
				url += '&s=' + encodeURI(search);
			}
			controller.load('<i class="fa fa-cog fa-spin"></i>');
			if(push == true){
				history.pushState(data, '', url);
			}
			$.get('<?php echo build_slug("ajax/ajax_search/media"); ?>', data , function(content){
				controller.load(content);
			});
		}
		$('#search-confirm').click(function(){
			var search = $('#search').val();
			if(search != ''){
				controller.page(1);
			}
		});
		$('#search').keypress(function(e){
			if( e.which == 13){
				var search = $('#search').val();
				if(search != ''){
					controller.page(1);
				}
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