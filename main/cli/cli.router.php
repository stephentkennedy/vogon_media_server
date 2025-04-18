<?php

//Register our available CLI commands
register_cli_commands();

$ds = data_store::get_instance();

$possible_commands = $ds->get('cli_commands', []);
$command = false;
if(isset($argv[1])){
    $command = $argv[1];
}

if(empty($command)){
    $command = 'help';
}

if(isset($possible_commands[$command])){
    $command = $possible_commands[$command];
    $cli_controller = $command['controller'];
    $ext = $command['ext'];
    load_cli($cli_controller, ['cli' => $cli, 'possible_commands' => $possible_commands], $ext);
}else if(isset($possible_commands['help'])){
    $cli->line('No command registered for: '.$command);
    $cli->line('Running help...');
    load_cli('help', ['cli' => $cli, 'possible_commands' => $possible_commands]);
}else{
   $cli->error('Unable to find command \''.$command.'\'');
}