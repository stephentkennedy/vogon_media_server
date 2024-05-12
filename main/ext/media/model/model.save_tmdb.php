<?php

if(isset($_POST['api_key'])){
    put_var('tmdb_api_key', $_POST['api_key'], 'string', true);
}