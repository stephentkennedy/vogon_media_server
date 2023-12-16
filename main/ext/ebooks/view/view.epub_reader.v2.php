<div id="cb-page-content" class="cb-fullscreen">
    <div id="cb-page" class="cb-fullscreen">
    </div>
    <div id="cb-controls" class="cb-fullscreen absolute">
        <a id="back" class="button" href="<?php 
            echo build_slug('', ['resume_search' => true], 'ebooks');
?>" title="Exit Viewer"><i class="fa fa-reply"></i></a>
        <button id="prev" title="Previous Page"><i class="fa fa-chevron-left"></i></button>
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
    #cb-page{
        background: #ffffff;
    }
    .cb-fullscreen{
        padding: 0;
        width: 100%;
        position: relative;
        min-height: 100vh;
        max-height: 100vh;
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
</style>
<script type="text/javascript">
    var epub_reader = {
        save_history_url: '<?php echo build_slug('ajax/ajax_save_history/ebooks'); ?>',
        get_history_url: '<?php echo build_slug('ajax/ajax_get_history/ebooks'); ?>',
        id: <?= $item['data_id']; ?>,
        book_url: '<?= $file_url; ?>',
        book: false,
        rendition: false,
        cur_page: 1,
        pages: 0,
        prev_cfi: false,
        cfi: false,
        fullscreen: false,
        toggle_fullscreen: function($dom){
            var content = $('html')[0];
            if(epub_reader.fullscreen == false){
                if(content.requestFullscreen()){
                    $dom.find('i').removeClass('fa-expand').addClass('fa-compress');
                    epub_reader.fullscreen = true;
                    screen.orientation.lock('landscape');
                }
            }else{
                if(document.exitFullscreen()){
                    epub_reader.fullscreen = false;
                    $dom.find('i').removeClass('fa-compress').addClass('fa-expand');
                    screen.orientation.unlock();
                }
            }
        },
        init: function(){
            this.book = ePub(this.book_url);
            this.rendition = this.book.renderTo('cb-page', {
                width: '100%',
                height: '100%'
            });
            epub_reader.bind();
            epub_reader.get_history();
        },
        next: function(){
            var promise = false;
            if(this.book.package.metadata.direction === 'rtl'){
                promise = this.rendition.prev();
            }else{
                promise = this.rendition.next();
            }
            return promise;
        },
        prev: function(){
            var promise = false;
            if(this.book.package.metadata.direction === 'rtl'){
                promise = this.rendition.next();
            }else{
                promise = this.rendition.prev();
            }
            return promise;
        },
        adjust_location: function(){
            var cfi = epub_reader.rendition.currentLocation().start.cfi;
            var data = {
                page: cfi,
                id: epub_reader.id
            };

            $.get(epub_reader.save_history_url, data);
        },
        get_history: function(){
            var data = {
                id: epub_reader.id
            };
            $.get(epub_reader.get_history_url, data, function(returned){
                if(returned.history != undefined && returned.history != false){
                    epub_reader.rendition.display(returned.history);
                }else{
                    epub_reader.rendition.display();
                }
            });
        },
        bind: function(){
            $('#next').click(function(){
                epub_reader.next().then(function(){
                    epub_reader.adjust_location();
                });
            });
            $('#prev').click(function(){
                epub_reader.prev().then(function(){
                    epub_reader.adjust_location();
                });
            });
            $('#toggle_fullscreen').click(function(){
                var $this = $(this);
                epub_reader.toggle_fullscreen($this);
            });

            //Keyboard Controls
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
                    case 70: //"f"
                        $('#toggle_fullscreen').trigger('click');
                        break;
                }
            });
        }
    };

    $(document).ready(function(){
        epub_reader.init();
    });
</script>