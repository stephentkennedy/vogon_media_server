<?php
if(
    !empty($_POST['rss_timeout'])
    && is_numeric($_POST['rss_timeout'])
){
    put_var('rss_timeout', $_POST['rss_timeout']);
}