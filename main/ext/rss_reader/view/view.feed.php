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
</style>
<?php //debug_d($feed); ?>
<div class="row">
<?php 
    $final_output = [];
    foreach($feed as $channel){
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
    });
</script>