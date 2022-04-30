<?php 
    if(!empty($_GET['t'])){
        $type = $_GET['t'];
    }
?><header><h1>E-Book Library</h1></header>
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
    var controller = {
        url: {
            'default': '<?php echo build_slug('ajax/ajax_search/ebooks'); ?>',
        },
        active_url: '<?php switch($type){
            default:
                echo 'default';
                break;
        } ?>',
        refresh_labels: function(id){
            //Label refresh function, $.get the new items
            $.get('<?php echo build_slug('ajax/ajax_item/ebooks'); ?>/' + id, function(data){
                var row = $('div.result-row[data-id="'+id+'"]');
				var name = row.find('span.result-one');
				var series = row.find('span.result-two');
                var sub_series = row.find('span.result-three');
				var author = row.find('span.result-four');
				var actions = row.find('span.result-five');
                console.log(data);
                name.html('<i class="fa fa-'+data['icon']+'"></i>&nbsp;'+data['data_name']);
                if(data['series_link'] != null){
                    series.html('<a href="' + data['series_link'] + '">' + data['parent_data_name'] + '</a>');
                }else{
                    series.html('[Unknown]');
                }
                if(data['author'] != null && data['author'] != ''){
                    author.html(data['author']);
                }else{
                    author.html('[Unknown]');
                }
                if(data['sub_series'] != null && data['sub_series'] != ''){
                    sub_series.html(data['sub_series']);
                }else{
                    sub_series.html('[Unknown]');
                }
            });
        },
        load: function(content){
            $('#ajax-output').html(content);
			$('.page-change').click(function(){
				var page = $(this).data('page');
				controller.page(page);
			});
            $('.ajax-form').off().click(function(){
				if($(this).data('href') != ''){
					var data = {
						format: 'ajax_form'
					};
					var id = $(this).data('id');
					$.get($(this).data('href'), data, function( returned ){
						app.ajax_form(returned, function(){
							controller.refresh_labels(id);
						});
					});
				}
			});
        },
        page: function(page, push){
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
    };

    $(document).ready(function(){
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