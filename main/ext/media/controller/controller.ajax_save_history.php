<?php
$id = $_GET['id'];
$time = $_GET['time'];
load_model('save_history', ['id' => $id, 'time' => $time], 'media');
echo 'saved';