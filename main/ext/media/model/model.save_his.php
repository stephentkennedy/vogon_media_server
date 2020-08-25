<?php

$seconds = $_POST['media_his_time'];
$js_seconds = $seconds * 1000;
put_var('media_his_time', $js_seconds, 'string', true);