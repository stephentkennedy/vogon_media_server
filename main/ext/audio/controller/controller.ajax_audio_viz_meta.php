<?php

/**
 * We need to save the following items to the meta table
 * level_graph
 * level_peak
 * max_bin
 * min_bin
 * 
 * We need to pass the audio's data_id as well
 */

if(
    !is_numeric($_POST['id'])
    || !is_numeric($_POST['max_bin'])
    || !is_numeric($_POST['min_bin'])
    || !is_numeric($_POST['level_peak'])
){
    return;
}

 $model_data = [
    'id' => $_POST['id'],
    'level_graph' => $_POST['level_graph'],
    'level_peak' => $_POST['level_peak'],
    'max_bin' => $_POST['max_bin'],
    'min_bin' => $_POST['min_bin']
 ];

 load_model('audio_visualizer_meta', $model_data, 'audio');