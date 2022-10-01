<header><h1>RSS Feed <a class="button" title="Edit Sources" href="<?= build_slug('sources', [], 'rss_reader'); ?>"><i class="fa fa-pencil"></i></a></h1></header>
<style>
    .feed-item > div{
        border: 1px solid var(--main-accent);
        border-radius: var(--border-radius);
        padding: 1em;
        margin: 0.5em;
        height: 100%;
    }
    .feed-item > div .img-holder{
        width: 100%;
        padding-top: 100%;
        background: var(--secondary-accent);
        position: relative;
        margin-bottom: 0.5em;
        overflow: hidden;
    }
    .feed-item > div .img-holder img,
    .feed-item > div .img-holder i{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .feed-item > div .img-holder img{
        width: auto;
        height: auto;
    }
    .errors .col{
        background-color: var(--error);
        color: var(--white);
        padding: 0.5rem;
        border-radius: var(--border-radius);
        border-bottom: 2px solid var(--secondary-accent);
        display: flex;
        flex-direction: row;
    }
    .errors .col > i{
        flex-grow: 0;
    }
    .errors .col span{
        flex-grow: 1;
        padding-left: 0.25rem;
    }
    .errors .col span.error-dismiss{
        padding: 0.25rem;
        display: flex;
        margin-top: -0.25em;
        margin-bottom: -0.25em;
        margin-right: -0.25em;
        transition: background var(--transition-speed) linear;
        border-radius: var(--border-radius);
        flex-grow: 0;
        background-color: transparent;
    }
    .errors .col span.error-dismiss:hover{
        background-color: rgba(var(--rgb-white), 0.5);
        cursor: pointer;
    }
    .errors .col + .col{
        margin-top: 0.25rem;
    }
    .errors .col:last-child{
        margin-bottom: 0.25rem;
    }
</style>
<?php if(!empty($feed['errors'])){?>
<div class="row errors">
    <?php 
    foreach($feed['errors'] as $error){
        echo '<div class="col col-ten"><i class="fa fa-';
        switch($error['type']){
            case 'timeout':
                echo 'clock-o';
                break;
            default:
                echo 'exclamation';
                break;
        }
        echo '"></i> <span>'.$error['message'].'</span> <span class="error-dismiss"><i class="fa fa-times"></i></span></div>';
    }
    ?>
</div>
<?php } ?>
<div class="row">
<?php 
    $final_output = [];
    foreach($feed as $fkey => $channel){
        if($fkey == 'errors'){
            continue;
        }
        foreach($channel['items'] as $key => $item){
            ob_start();
            echo '<div class="col col-two feed-item" target="_blank" href="'.$item['link'].'"><div>';
                echo '<div class="img-holder">';
                if(empty($item['image'])){
                    echo '<i class="fa fa-eye-slash"></i>';
                }else{
                    echo '<img class="lazy" title="'.$item['image']['title'].'" alt="'.$item['image']['desc'].'" data-src="'.$item['image']['src'].'">';
                }
                echo '</div>';
                echo '<a class="button" href="'.$item['link'].'" target="_blank"><h4>'.$item['title'].'</h4></a>';
                echo '<p>'.$item['desc'].'</p>';
                echo '<span class="source"><a href="'.$channel['link'].'" target="_blank">'.$channel['title'].'</a></span>';
                echo '<span class="date">'.nice_date($item['date']).'</span>';
            echo '</div></div>';
            $output = ob_get_clean();
            $time_key = strtotime($item['date']);
            if(empty($final_output[$time_key])){
                $final_output[$time_key] = $output;
            }else{
                $final_output[$time_key.'-'.$item['title']] = $output;
            }
            if($key >= 4){
                break;
            }
        }
    }
    krsort($final_output);
    foreach($final_output as $output){
        echo $output;
    }
?>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        lazy.init('.lazy');

        $('.error-dismiss').click(function(){
            $(this).parent().remove();
        });
    });
</script>