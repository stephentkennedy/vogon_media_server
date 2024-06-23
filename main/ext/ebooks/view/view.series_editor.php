<header><h1>Series Editor</h1></header>
<div class="row wide" style="display: flex">
    <div class="col col-ten"><p><strong>Intructions:</strong><br>
    Find an existing series or create one in the left most pane. Series members will appear in the middle pane. Search for items to add in the left most pane.<br>
    Arrow Keys to select. CTRL + Arrow Keys to move item (up/down to change order, right/left to move between panes.)<br>
    <strong>Warning:</strong> Long series may not load correctly in this editor.</p></div>
</div>
<div class="row wide" style="display: flex">
    <fieldset id="series_meta" class="col col-two">
        <legend>Series</legend>
        <label for="series">Series Name</label>
        <input id="series" value="" />
        <label for="sub_series">Sub Series Name</label>
        <input id="sub_series" value="" />
        <button id="save_data"><i class="fa fa-floppy-o"></i> Save</button>
    </fieldset>
    <fieldset id="series_members" class="scroll col col-four">
        <legend>Series Members</legend>
        <div id="series_results" class="results_column"></div>
    </fieldset>
    <fieldset id="other_items" class="scroll col col-four">
        <legend>Other Items</legend>
        <div class="search_form">
            <label for="search">Search</label>
            <input type="text" id="search" placeholder="Search...">
            <label for="type">Type</label>
            <select id="type">
                <option value="">All</option>
                <option value="cbz">CBZ</option>
                <option value="pdf">PDF</option>
                <option value="epub">EPUB</option>
            </select>
            <button type="button" id="search-confirm">Search</button>
        </div>
        <div class="results_column" id="ajax-output"></div>
    </fieldset>
</div>
<style>
    #series_meta{
        position: sticky;
        top: 0;
    }
    .scroll .results_column{
        overflow: auto;
    }
    #ajax-output{
        padding-top: 20px;
    }
    .result-row.active{
        background: var(--main-accent);
        color: var(--white);
    }
</style>
<script type="text/javascript">
    var controller = {
        url: {
            'default': '<?php echo build_slug('ajax/ajax_search/ebooks'); ?>',
            'epub': '<?php echo build_slug('ajax/ajax_search/ebooks', [
                'type' => 'epub'
            ]); ?>',
            'pdf': '<?php echo build_slug('ajax/ajax_search/ebooks', [
                'type' => 'pdf'
            ]); ?>',
            'cbz': '<?php echo build_slug('ajax/ajax_search/ebooks', [
                'type' => 'cbz'
            ]); ?>',
        },
        active_url: '<?php
            $type = 'default';
        switch($type){
            default:
                echo 'default';
                break;
            case 'epub':
                echo 'epub';
                break;
            case 'pdf':
                echo 'pdf';
                break;
            case 'cbz':
                echo 'cbz';
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
                name.html('<i class.results="fa fa-'+data['icon']+'"></i>&nbsp;'+data['data_name']);
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
            editor.bind();
        },
        page: function(page, push){
            if(push == undefined){
				push = true;
			}
			var data = {
				'format': 'search_results',
				'page': page,
                'rpp': 25
				//'type': controller.active_url
			};
			var search = $('#search').val();
            var type = $('#type').val();
            var not_series = $('#series').val();
            var not_sub_series = $('#sub_series').val();
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
            if(not_series != ''){
                //data['not_series'] = not_series;
            }
            if(not_sub_series != ''){
                //data['not_sub_series'] = not_sub_series;
            }
			url += '&t=' + encodeURI(controller.active_url);
			controller.load('<i class="fa fa-cog fa-spin"></i>');
			if(push == true){
				history.pushState(data, '', url);
			}
			$.get(controller.url[controller.active_url], data , function(content){
				controller.load(content);
			});
            //var url = new URL(window.location);
            //var s = url.search;
            //var ls = window.localStorage;
            //ls.setItem('ebook_search', s);
        }
    };

    var series_controller = {
        url: controller.url.default,
        page: function(page){
			var data = {
				'format': 'search_results',
				'page': page,
                'rpp': -1
				//'type': controller.active_url
			};
			var search = $('#search').val();
            var type = $('#type').val();
            var not_series = $('#series').val();
            var not_sub_series = $('#sub_series').val();
			var url = '?p=' + page;
			if(search != ''){
				//data['search'] = search;
				//url += '&s=' + encodeURI(search);
			}
            if(not_series != ''){
                data['series'] = not_series;
            }
            if(not_sub_series != ''){
                data['sub_series'] = not_sub_series;
            }
			//url += '&t=' + encodeURI(series_controller.url);
			series_controller.load('<i class="fa fa-cog fa-spin"></i>');
			$.get(series_controller.url, data , function(content){
				series_controller.load(content);
                controller.page(1);
			});
            //var url = new URL(window.location);
            //var s = url.search;
            //var ls = window.localStorage;
            //ls.setItem('ebook_search', s);
        },
        load: function(content){
            $('#series_results').html(content);
        },
        on_change: function(){
            var series = $('#series').val();
            if(series == ''){
                series_controller('');
            }else{
                series_controller.page(1);
            }
        }
    };

    var editor = {
        active_id: 0,
        cur_column: '',
        columns: {
            'series_results': 'ajax-output',
            'ajax-output': 'series_results'
        },
        select: function(dom){
            var $this = jQuery(dom);
            jQuery('.result-row').removeClass('active');
            jQuery('.results_column').removeClass('active');
            if(!$this.hasClass('result-row')){
                $this = $this.closest('result-row');
            }
            var active_id = $this.data('id');
            editor.active_id = active_id;
            $this.addClass('active');
            var $parent = $this.closest('.results_column');
            $parent.addClass('active');
            editor.cur_column = $parent.attr('id');
            editor.scroll_to_active();
        },
        select_next: function(){
            if(editor.active_id == 0){
                return;
            }
            var $cur = jQuery('.result-row.active[data-id="' + editor.active_id + '"]');
            var $next = $cur.next('.result-row:not(.result-header)');
            if($next.length > 0){
                editor.select($next[0]);
            }else{
                var $parent = $cur.closest('.results_column');
                editor.select($parent.find('.result-row:not(.result-header)')[0]);
            }
        },
        select_previous: function(){
            if(editor.active_id == 0){
                return;
            }
            var $cur = jQuery('.result-row.active[data-id="' + editor.active_id + '"]');
            var $next = $cur.prev('.result-row:not(.result-header)');
            if($next.length > 0){
                editor.select($next[0]);
            }else{
                var $parent = $cur.closest('.results_column');
                editor.select($parent.find('.result-row:not(.result-header)').last()[0]);
            }
        },
        change_column: function(){
            if(editor.active_id == 0){
                return;
            }
            var $this = jQuery('.result-row.active[data-id="' + editor.active_id + '"]');
            var new_parent_id = '#' + editor.columns[editor.cur_column];
            $this.appendTo(new_parent_id);
            var $new_parent = jQuery(new_parent_id);
            jQuery('results').removeClass('active');
            $new_parent.addClass('active');
            editor.scroll_to_active();
        },
        move_up: function(){
            if(editor.active_id == 0){
                return;
            }
            var $this = jQuery('.result-row.active[data-id="' + editor.active_id + '"]');
            var $prev = $this.prev('.result-row:not(.result-header)');
            if($prev.length > 0){
                $this.insertBefore($prev);
            }
            editor.scroll_to_active();
        },
        move_down: function(){
            if(editor.active_id == 0){
                return;
            }
            var $this = jQuery('.result-row.active[data-id="' + editor.active_id + '"]');
            var $prev = $this.next('.result-row:not(.result-header)');
            if($prev.length > 0){
                $this.insertAfter($prev);
            }
            editor.scroll_to_active();
        },
        switch_column: function(){
            if(editor.active_id == 0){
                return;
            }
            var $this = jQuery('.result-row.active[data-id="' + editor.active_id + '"]');
            var new_parent_id = '#' + editor.columns[editor.cur_column];
            $new_parent = jQuery(new_parent_id);
            $select = $new_parent.find('.result-row:not(.result-header)').first();
            editor.select($select[0]);
        },
        check_series: function(){
            if(jQuery('#series').val() != ''){
                return true
            }
            return false;
        },
        scroll_to_active: function(){
            return;
            if(editor.active_id == 0){
                return;
            }
            var $this = jQuery('.result-row.active[data-id="' + editor.active_id + '"]');
            $(document.body).animate({
                scrollTop: $this.offset().top
            }, 500);
        },
        get_series_data: function(){
            var $selected = jQuery('#series_results').find('.result-row');

            var data = [];

            $selected.each(function(){
                //console.log(this);
                var $this = jQuery(this);
                data.push($this.data('id'));
            });

            var series = jQuery('#series').val();
            var sub_series = jQuery('#sub_series').val();

            return {
                'series': series,
                'sub_series': sub_series,
                'members': data
            };
        },
        bind: function(){
            jQuery('.result-row').off('click').click(function(){
                editor.select(this);
            });
            var $result = jQuery('#ajax-output .result-row').first();
            if($result.length > 0){
                editor.select($result[0]);
            }
            jQuery('#save_data').off().click(function(e){
                e.preventDefault();
                jQuery('#save_data').append('<i class="fa fa-fw fa-cog fa-spin"></i>');

                var to_post = editor.get_series_data();


                jQuery.post('<?php echo build_slug('ajax/ajax_save_series/ebooks'); ?>', to_post).done(function(returned){
                    jQuery('#save_data').find('.fa-cog').remove();
                });
            });
        },
        init: function(){
            $(window).keydown(function(e){
                switch(e.which){
                    case 38: // Arrow Up
                        e.preventDefault();
                        if(!e.ctrlKey){
                            editor.select_previous();
                        }else{
                            editor.move_up();
                        }
                        break;
                    case 40: // Arrow Down
                        e.preventDefault();
                        if(!e.ctrlKey){
                            editor.select_next();
                        }else{
                            editor.move_down();
                        }
                        break;
                    case 37: //Left Arrow
                    case 39: //Right Arrow
                        e.preventDefault();
                        if(
                            editor.check_series()
                            && e.ctrlKey
                        ){
                            editor.change_column();
                        }else if(editor.check_series()){
                            editor.switch_column();
                        }
                        break;
                }
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
            if(params['t'] != undefined){
                $('#type').val(params['t']);
            }
            controller.page(page, false);
        }
        $('#series').autocomplete({
            source: function(request, response){
                $.get('<?php echo build_slug('ajax/ajax_series/ebooks'); ?>', {'search': request.term}).done(function(data){
                    response($.map(data, function(item){
						return{
							label: item.data_name,
							value: item.data_name
						}
					}));
                })
            }
        });
        $('#series').change(function(){
            series_controller.on_change();
        });
        $('#sub_series').autocomplete({
            source: function(request, response){
                $.get('<?php echo build_slug('ajax/ajax_sub_series/ebooks'); ?>', {'search': request.term}).done(function(data){
                    response($.map(data, function(item){
						return{
							label: item.data_meta_content,
							value: item.data_meta_content
						}
					}));
                })
            }
        });
        $('#sub_series').change(function(){
            series_controller.on_change();
        });
        editor.init();
    });
</script>