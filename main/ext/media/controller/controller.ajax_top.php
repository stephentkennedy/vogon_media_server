<?php
echo shell_exec('uptime');
echo PHP_EOL.'--Memory--'.PHP_EOL.PHP_EOL;
echo shell_exec('free -m');