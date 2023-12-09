<script src="<?php echo build_slug('js/pdf/pdf.js'); ?>"></script>
<div id="cb-page-content" class="cb-fullscreen">
    <canvas id="cb-canvas"></canvas>
    <div id="cb-page" class="cb-fullscreen">
    </div>
    <div id="cb-controls" class="cb-fullscreen absolute">
        <a id="back" class="button" href="<?php 
            echo build_slug('', ['resume_search' => true], 'ebooks');
?>" title="Exit Viewer"><i class="fa fa-reply"></i></a>
        <button id="prev" title="Previous Page"><i class="fa fa-chevron-left"></i></button>
        <a id="compat_mode" class="button" href="<?php

            $url_filename = str_replace([
                ROOT,
                '#'
            ], [
                '',
                urlencode('#')
            ], $item['data_content']);

            echo build_slug('compat_view/'.$item['data_id'], [
                'file' => $url_filename
            ], 'ebooks');
        ?>">Switch Viewer</a>
        <input id="page_num" value="<?php
            if(isset($_GET['page'])){
                echo $_GET['page'];
            }else{
                echo '1';
            }
        ?>" type="number" min="1">
        <a id="toggle_fullscreen" class="button"><i class="fa fa-expand"></i></a>
        <button id="next" title="Next Page"><i class="fa fa-chevron-right"></i></button>
    </div>
</div>
<style>
    html{
        /*height: 100%;*/
        width: 100%;
    }
    body{
        background-color: #222222;
    }
    .cb-fullscreen{
        padding: 0;
        width: 100%;
        position: relative;
        min-height: 100vh;
    }
    .cb-fullscreen img{
        display: block;
        margin: 0 auto;
        max-width: 100%;
    }
    .cb-fullscreen i.icon-center{
        position: absolute;
        left: calc(50% - 36px);
        top: calc(50% - 36px);
        transform: translate(-50%, -50%);
        font-size: 4rem;
        color: #fff;
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
    #cb-controls a:hover{
        opacity: 1;
    }
    #cb-controls button{
        opacity: 0.25;
    }
    #cb-controls #next, #cb-controls #prev{
        width: 96px;
        height: calc(100vh - 96px);
        top: 96px;
        position: absolute;
    }
    #cb-controls #toggle_fullscreen{
        left: auto;
        right: 0;
    }
    /*#cb-controls #prev{
        height: calc(100vh - 96px);
        top: 96px;
    }*/
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
    #cb-controls #compat_mode{
        width: calc(100% - 192px);
        left: 92px;
        transition: opacity 0.4s ease;
    }
    #cb-controls #page_num{
        position: absolute;
        width: 4rem;
        text-align: center;
        top: auto;
        bottom: 0;
        left: 50%;
        opacity: 0.25;
        transform: translateX(-50%);
        transition: opacity 0.4s ease;
    }
    #cb-controls #page_num:focus,
    #cb-controls #page_num:hover{
        opacity: 1;
    }
    #cb-canvas{
        position: absolute;
        top: 0;
        left: 50%;
        max-width: 100vw;
        transform: translateX(-50%);
    }
</style>
<script type="text/javascript">
    var {pdfjsLib} = globalThis;
    
    var pdf_reader = {
        pdfData: false,
        pdfDoc: null,
        //id: 19007, //Batman Death in the Family Book 1
        id: <?php echo $item['data_id']; ?>,
        pages: 0,
        cur_page: 0,
        loaded: false,
        scale: 1, //Need to find a way to calc this
        data_url: '<?php echo build_slug('ajax/ajax_pdf_data/ebooks'); ?>',
        worker_url: '<?php echo build_slug('js/pdf/pdf.worker.js'); ?>',
        save_history_url: '<?php echo build_slug('ajax/ajax_save_history/ebooks'); ?>',
        get_history_url: '<?php echo build_slug('ajax/ajax_get_history/ebooks'); ?>',
        root_url: '<?php echo build_slug('view', [], 'ebooks'); ?>',
        next_issue_url: '<?php echo build_slug('ajax/ajax_get_next_issue/ebooks'); ?>',
        next_issue: false,
        lib: pdfjsLib,
        rendering: false,
        queued_page: null,
        fullscreen: false,
        toggle_fullscreen: function($dom){
            var content = $('html')[0];
            if(pdf_reader.fullscreen == false){
                if(content.requestFullscreen()){
                    $dom.find('i').removeClass('fa-expand').addClass('fa-compress');
                    pdf_reader.fullscreen = true;
                    screen.orientation.lock('landscape');
                }
            }else{
                if(document.exitFullscreen()){
                    pdf_reader.fullscreen = false;
                    $dom.find('i').removeClass('fa-compress').addClass('fa-expand');
                    screen.orientation.unlock();
                }
            }
        },
        get_next_issue: function(){
            $.get(pdf_reader.next_issue_url, {
                id: pdf_reader.id
            }, function(returned){
                if(returned.error != false){
                    pdf_reader.next_issue = pdf_reader.root_url + '/' + returned['data_id'];
                }
            });
        },
        get_history: function(){
            $.get(pdf_reader.get_history_url,{id: pdf_reader.id}, function(returned){
                if(returned.history != undefined && returned.history != false){
                    pdf_reader.render_page(Number(returned.history))
                }else{
                    pdf_reader.render_page(pdf_reader.cur_page);
                }
            });
        },
        save_history: function(){
            var vars = pdf_reader.get_vars();
            var page = vars['page'];
            $.get(pdf_reader.save_history_url, {id: pdf_reader.id, page: page});
        },
        get_data: function(){
            return $.post(this.data_url, {data_id: this.id}, function(returned){
                pdf_reader.pdfData = atob(returned.pdf);
            }).promise();
        },
        get_vars: function(){
            var url = new URL(window.location);
            var s = url.search;
            if(s.length == 0){
                return false;
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
        blank_page: function(){
            var canvas = $('#cb-canvas');
            var ctx = canvas[0].getContext("2d");
            ctx.globalCompositeOperation = 'source-over';
            ctx.fillStyle = '#000000';
            ctx.fillRect(0,0,canvas.width,canvas.height);
            $('#cb-page').html('<i class="fa fa-cog fa-spin icon-center"></i>');
        },
        clear_page: function(){
            $('#cb-page').html('');
        },
        get_canvas: function(){
            var canvas = $('#cb-canvas');
            return canvas[0];
        },
        get_canvas_ctx: function(){
            var canvas = this.get_canvas();
            var ctx = canvas.getContext("2d");
            ctx.globalCompositeOperation = 'source-over';
            return ctx;
        },
        error_page: function(){
            $('#cb-page').html('<i style="color: red !important;" class="fa fa-times icon-center"></i>');
        },
        calc_scale: function(page){
            var desiredWidth = $(window).width();
            var viewport = page.getViewport({scale: 1});
            var scale = desiredWidth / viewport.width;
            return scale;
        },
        init: function(){
            this.blank_page();
            this.bind_controls();
            this.get_next_issue();
            window.onpopstate = pdf_reader.pop_history;
            var params = this.get_vars();
            this.lib.GlobalWorkerOptions.workerSrc = this.worker_url;
            if(typeof params['page'] != 'undefined'){
                this.cur_page = Number(params['page']);
            }else{
                this.cur_page = 1;
            }
            this.get_data().done(function(){
                //pdf_reader.render_page(pdf_reader.cur_page);
                pdf_reader.lib.getDocument({data: pdf_reader.pdfData}).promise.then(function(pdfDoc_){
                    pdf_reader.pdfDoc = pdfDoc_;
                    pdf_reader.loaded = true;
                    $('#page_num').attr('max', pdf_reader.pdfDoc.numPages);
                    //pdf_reader.render_page(pdf_reader.cur_page);
                    pdf_reader.get_history();
                });
            });
        },
        render_page: function(page_num){
            document.body.scrollTop = document.documentElement.scrollTop = 0;
            $('#page_num').val(page_num);
            this.queued_page = null; //We clear the queue at the start of the rendering process.
            this.rendering = true;
            var canvas = this.get_canvas();
            var ctx = this.get_canvas_ctx();
            var params = this.get_vars();
            if(
                typeof params.page == 'undefined'
                || params.page != page_num
            ){
                this.push_history(page_num);
            }
            this.pdfDoc.getPage(page_num).then(function(page){
                var page_scale = pdf_reader.calc_scale(page);
                var viewport = page.getViewport({scale: page_scale});
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                var rctx = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                var renderTask = page.render(rctx);
                renderTask.promise.then(function(){
                    pdf_reader.rendering = false;
                    pdf_reader.clear_page();
                    if(pdf_reader.queued_page != null){
                        pdf_reader.render(pdf_reader(queued_page));
                    }
                });
            });
        },
        queue_render: function(page_num){
            if(this.rendering == true){
                this.queued_page = page_num;
            }else{
                this.render_page(page_num);
            }
        },
        next_page: function(){
            if(!this.loaded){
                return;
            }
            if(
                this.cur_page >= this.pdfDoc.numPages
            ){
                if(this.next_issue != false){
                    window.location = this.next_issue;
                    return;
                }
                return;
            }
            this.cur_page++;
            this.queue_render(this.cur_page);
        },
        prev_page: function(){
            if(
                !this.loaded
                || (this.cur_page - 1) <= 0
            ){
                return;
            }
            this.cur_page = this.cur_page - 1;
            this.queue_render(this.cur_page);
        },
        bind_controls: function(){
            $('#prev').click(function(){
                pdf_reader.prev_page();
            });
            $('#next').click(function(){
                pdf_reader.next_page();
            });
            $('#page_num').on('change', function(){
                if(pdf_reader.loaded == false){
                    return;
                }
                
                var $this = $(this);
                var val = Number($this.val());
                if(
                    val >= 1
                    && val <= pdf_reader.pdfDoc.numPages
                ){
                    pdf_reader.cur_page = val;
                    pdf_reader.queue_render(val);
                }
            });
            $('#toggle_fullscreen').click(function(){
                var $this = $(this);
                pdf_reader.toggle_fullscreen($this);
            });

            $(window).keydown(function(e){
                switch(e.which){
                    case 39: //Right Arrow
                    case 34: //Page Down
                        $('#next').trigger('click');
                        break;
                    case 37: //Left Arrow
                    case 33: //Page Up
                        $('#prev').trigger('click');
                        break;
                    case 36: //Home
                        $('.cb-page').animate({
                            scrollTop: 0
                        }, 200);
                        break;
                    case 35: //End
                        $('.cb-page').animate({
                            scrollTop: $('.cb-page').height()-$(window).height()
                        }, 200);
                        break;
                    case 112: //F1
                    case 72: //"h"
                        pdf_reader.help();
                        break;
                    case 70: //"f"
                        $('#toggle_fullscreen').trigger('click');
                        break;
                    default:
                        console.log('Keycode: ' + e.which);
                        break;
                }
            });
        },
        push_history: function(page){
            var url = '?page=' + page;
            var data = {
                'format': 'HTML',
                'page': page,
            };
            history.pushState(data, '', url);
            pdf_reader.save_history();
        },
        pop_history: function(e){
            if(typeof e == 'undefined'){
                return;
            }
            if(
                e.state == null
                || e.state.page != undefined
            ){
                var params = pdf_reader.get_vars();
                var page = params.page;
            }else{
                var page = e.state.page;
            }
            pdf_reader.queue_render(Number(page));
        },
    };

    jQuery(document).ready(function(){
        pdf_reader.init();
    });
</script>