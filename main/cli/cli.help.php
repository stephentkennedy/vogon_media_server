<?php

$title = $cli->format_string('Vogon CLI Help', 'bold');

$cli->line('__'.$title.'__');
$cli->line('The following commands are registered');
foreach($possible_commands as $command => $options){
    $display_command = $cli->colorize_string($command, 'yellow');
    $cli->line($display_command.': '.$options['desc']);
    if(!empty($options['flags'])){
        foreach($options['flags'] as $flag => $desc){
            $display_flag = $cli->colorize_string('    --'.$flag, 'light_blue');
            $cli->line($display_flag.': '.$desc);
        }
    }
}