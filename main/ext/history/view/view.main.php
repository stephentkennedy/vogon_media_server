<header><h1>User History</h1></header>
<div id="ajax-output">
	Loading Content...
</div>
<script type="text/javascript">
    var controller = {
        url: {
            'default': '<?php echo build_slug('ajax/ajax_search/history'); ?>',
        },
        active_url: '<?php switch($type){
            default:
                echo 'default';
                break;
        } ?>',
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
				//'type': controller.active_url
			};
			var search = $('#search').val();
            var type = $('#type').val();
            if(
                type != ''
                && typeof controller.url[type] != 'undefined'
            ){
                controller.active_url = type;
            }else{
                controller.active_url = 'default';
            }
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
            var url = new URL(window.location);
            var s = url.search;
            var ls = window.localStorage;
            ls.setItem('ebook_search', s);
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
            if(params['t'] != undefined){
                $('#type').val(params['t']);
            }
            controller.page(page, false);
        }
    });
</script>