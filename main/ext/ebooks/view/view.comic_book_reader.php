<div id="cb-page-content" class="cb-fullscreen">
    <canvas id="cb-page" class="cb-fullscreen">
    </canvas>
    <div id="cb-controls" class="cb-fullscreen absolute">
        <a id="back class="button" href="<?php 
        if(!empty($_SERVER['HTTP_REFERER'])){
            echo $_SERVER['HTTP_REFERER'];
        }else{
            echo build_slug('', [], 'ebooks');
        }
?>" title="Exit Viewer"><i class="fa fa-reply"></i></a>
        <button id="prev" title="Previous Page"><i class="fa fa-chevron-left"></i></button>
        <button id="zoom-in" title="Zoom-in"><i class="fa fa-plus"></i></button>
        <button id="zoom-out" title="Zoom-out"><i class="fa fa-minus"></i></button>
        <button id="next" title="Next Page"><i class="fa fa-chevron-right"></i></button>
    </div>
</div>
<style>
    html{
        /*height: 100%;*/
        width: 100%;
    }
    .cb-fullscreen{
        padding: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
    }
    .absolute{
        position: absolute;
        top: 0;
        left: 0;
    }
    .cb-fullscreen#cb-controls{
        position: fixed;
        width: 100%;
        height: 100vh;
    }
    #cb-controls a{
        opacity: 0.25;
        position: absolute;
        top: 0;
        left: 0;
        width: 96px;
        height: 96px;
        text-align: center;
        display: flex !important;
        flex-direction: column;
        justify-content: center;
        padding: 0;
    }
    #cb-controls button{
        opacity: 0.25;
    }
    #cb-controls #next, #cb-controls #prev{
        width: 96px;
        height: 100vh;
        top: 0;
        position: absolute;
    }
    #cb-controls #prev{
        height: calc(100vh - 96px);
        top: 96px;
    }
    #cb-controls #next{
        right: 0;
    }
    #cb-controls #prev{
        left: 0;
    }
    #cb-controls #zoom-in, #cb-controls #zoom-out{
        position: absolute;
        width: calc(100% - 192px);
        left: 96px;
        height: 96px;
    }
    #cb-controls #zoom-in{
        top: 0;
    }
    #cb-controls #zoom-out{
        bottom: 0;
    }
</style>
<script type="text/javascript">
    var cb_reader = {
        c_id: <?php echo $item['data_id']; ?>,
        c_format: '<?php echo $item['data_type']; ?>',
        page_url: '<?php echo build_slug('ajax/ajax_cb_image_data/ebooks'); ?>',
        next_issue_url: '<?php echo build_slug('ajax/ajax_get_next_issue/ebooks'); ?>',
        root_url: '<?php echo build_slug('view', [], 'ebooks'); ?>',
        save_history_url: '<?php echo build_slug('ajax/ajax_save_history/ebooks'); ?>',
        get_history_url: '<?php echo build_slug('ajax/ajax_get_history/ebooks'); ?>',
        next_issue: false,
        page: new Image(),
        scale: 1,
        current_page: false,
        page_index: 0,
        pages: [],
        get_vars: function(){
            var url = new URL(window.location);
            var s = url.search;
            if(s.length == 0){
                return {'page': 0};
            }
            s = s.split('&');
            s[0] = s[0].replace('?', '');
            var params = {};
            for(var i = 0; i < s.length; i++){
                var temp = s[i].split('=');
                params[temp[0]] = decodeURI(temp[1]);
            }
            return params;
        },
        get_page: function(page){
            if(page == undefined){
                page = 0;
            }
            cb_reader.page_index = Number(page);
            cb_reader.blank_page();
            $.ajaxSetup({timeout: 0, cache: false, dataType: 'json'});
            $.get(cb_reader.page_url, {page: page, id: cb_reader.c_id}, function(returned){
                if((returned.error == undefined || returned.error == false) && returned != '' && returned.image_data != undefined){
                    cb_reader.render_page(returned);
                    var params = cb_reader.get_vars();
                    if(page != params[page]){
                        cb_reader.push_history(page);
                    }
                }else{
                    console.log(returned);
                }
            });
        },
        blank_page: function(){
            var canvas = $('#cb-page');
            var ctx = canvas[0].getContext("2d");
            ctx.globalCompositeOperation = 'source-over';
            ctx.fillStyle = '#000000';
            ctx.fillRect(0,0,canvas.width,canvas.height);
        },
        render_page: function(page_object){
            cb_reader.current_page = false;
            cb_reader.scale = Number(cb_reader.scale.toFixed(2));
            cb_reader.current_page = page_object;
            cb_reader.page.src = page_object.image_data;
            cb_reader.page.onload = function(){
                var page_w = cb_reader.page.width;
                var page_h = cb_reader.page.height;
                var page_r = page_h / page_w;
                if(
                    cb_reader.scale == 1 
                    && page_w > $(window).width()
                ){
                    cb_reader.scale = $(window).width() / page_w;
                }
                //Scale the image according to our scale setting
                var draw_w = page_w * cb_reader.scale;
                var draw_h = draw_w * page_r;
                var x_offset = 0;
                var canvas = $('#cb-page');
                canvas.prop('width', $(window).width());
                if(draw_w > $(window).width()){
                    canvas.width(draw_w);
                }else if(draw_w < $(window).width()){
                    var window_half = $(window).width() / 2;
                    var page_half = draw_w / 2;
                    x_offset = window_half - page_half;
                }
                
                canvas.prop('height', draw_h);
                var ctx = canvas[0].getContext("2d");
                ctx.globalCompositeOperation = 'source-over';
                ctx.fillStyle = '#000000';
                ctx.fillRect(0,0,canvas.width,canvas.height);
                ctx.drawImage(cb_reader.page,0,0,page_w,page_h, 0 + x_offset, 0, draw_w, draw_h);
            }
        },
        bind_controls: function(){
            $('#prev').click(function(){
                if(cb_reader.page_index > 0){
                    cb_reader.get_page(cb_reader.page_index - 1);
                    $(window).scrollTop(0);
                }
            });
            $('#next').click(function(){
                if(cb_reader.page_index < (cb_reader.current_page.count - 1)){
                    cb_reader.get_page(cb_reader.page_index + 1);
                    $(window).scrollTop(0);
                }else if(cb_reader.page_index >= (cb_reader.current_page.count - 1) && cb_reader.next_issue != false){
                    window.location = cb_reader.next_issue;
                }
            });
            $('#zoom-in').click(function(){
                cb_reader.scale += 0.05;
                cb_reader.render_page(cb_reader.current_page);
            });
            $('#zoom-out').click(function(){
                cb_reader.scale += -1 * 0.05;
                cb_reader.render_page(cb_reader.current_page);
            });
        },
        push_history: function(page){
            var url = '?page=' + page;
            var data = {
                'format': 'HTML',
                'page': page,
            };
            history.pushState(data, '', url);
        },
        pop_history: function(e){
            if(
                e.state == null
                || e.state.page != undefined
            ){
                var params = cb_reader.get_vars();
                var page = params.page;
            }else{
                var page = e.state.page;
            }
            cb_reader.get_page(page);
        },
        get_next_issue: function(){
            $.get(cb_reader.next_issue_url, {id: cb_reader.c_id}, function(returned){
                if(returned.error != false){
                    cb_reader.next_issue = cb_reader.root_url + '/' + returned['data_id'];
                }
            });
        },
        init: function(){
            var params = cb_reader.get_vars();
            cb_reader.get_page(params['page']);
            window.onpopstate = cb_reader.pop_history;
            cb_reader.bind_controls();
            cb_reader.get_next_issue();
        }
    };

    $(document).ready(function(){
        cb_reader.init();
    });
</script>