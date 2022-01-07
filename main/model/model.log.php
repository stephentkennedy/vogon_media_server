<?php

$date = date('m_d_Y');
$time = date('H:i:s');

$filename = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR .'logs' . DIRECTORY_SEPARATOR . '.'.$log.'_log_'.$date;

$write = <<<DOC
---
Source: {$source}
Server Time: {$time}
Message: {$message}
---

DOC;

$h = fopen($filename, 'a');
fwrite($h, $write);
fclose($h);