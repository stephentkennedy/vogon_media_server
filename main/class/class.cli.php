<?php
/**
 * This class provides a number of useful functions for writing CLI php scripts.
 */
class cli{

    public $control = "\033[";
    public $end_control = "m";

    public $color_table = [
        'default' => 39,
        'black' => 30,
        'red' => 31,
        'green' => 32,
        'yellow' => 33,
        'blue' => 34,
        'magenta' => 35,
        'cyan' => 36,
        'light_gray' => 37,
        'dark_gray' => 90,
        'light_red' => 91,
        'light_green' => 92,
        'light_yellow' => 93,
        'light_blue' => 94,
        'light_magenta' => 95,
        'light_cyan' => 96,
        'white' => 97,
        'bg_default' => 49,
        'bg_black' => 40,
        'bg_red' => 41,
        'bg_green' => 42,
        'bg_yellow' => 43,
        'bg_blue' => 44,
        'bg_magenta' => 45,
        'bg_cyan' => 46,
        'bg_light_gray' => 47,
        'bg_dark_gray' => 100,
        'bg_light_red' => 101,
        'bg_light_green' => 102,
        'bg_light_yellow' => 103,
        'bg_light_blue' => 104,
        'bg_light_magenta' => 105,
        'bg_light_cyan' => 106,
        'bg_white' => 107
    ];

    public $format_table = [
        'bold' => 1,
        'dim' => 2,
        'underline' => 4,
        'blink' => 5,
        'invert' => 7,
        'hidden' => 8,
        'reset_all' => 0,
        'reset_bold' => 21,
        'reset_dim' => 22,
        'reset_underlined' => 24,
        'reset_blink' => 25,
        'reset_reverse' => 27,
        'reset_hidden' => 28
    ];

    public $on_cli = false;

    public function __construct(){
        global $argc;
        if(isset($argc)){
            $this->on_cli = true;
        }
    }

    public function get_flag($flag_name, $default = null){
        if(!$this->on_cli){
            return false;
        }
        global $argv;

        $flags = [];
        foreach($argv as $var){
            if(substr($var, 0, 2) == '--'){
                if(stristr($var, '=') !== false){
                    $array = explode('=', $var);
                    $value = $array[1];
                    $var = $array[0];
                }else{
                    $value = true;
                }
                $flags[substr($var, 2)] = $value;
            }
        }

        if(isset($flags[$flag_name])){
            return $flags[$flag_name];
        }

        return $default;
    }

    public function dir_string_to_path($dir_path){
        if(substr($dir_path, 0, strlen('./')) == './'){
            $dir_path = getcwd() . substr($dir_path, strlen('.'));
        }else if(substr($dir_path, 0, strlen('~/')) == '~/'){
            $dir_path = getenv('HOME') . substr($dir_path, strlen('~'));
        }
        return $dir_path;
    }

    public function colorize_string($text, $color){
        if(!empty($this->color_table[$color])){
            $text = $this->control.$this->color_table[$color].$this->end_control.$text.$this->control.$this->format_table['reset_all'].$this->end_control;
        }

        return $text;
    }

    public function get_user_input($message){
        if(!defined('STDIN')){
            return;
        }
        $this->print(trim($message).' ');
        $return = fgets(STDIN, );
        $return = trim($return);
        return $return;
    }

    public function format_string($text, $format){
        if(
            !empty($this->format_table[$format])
            && substr($format, 0, strlen('reset')) != 'reset'
        ){
            $text = $this->control.$this->format_table[$format].$this->end_control.$text.$this->control.$this->format_table['reset_all'].$this->end_control;
        }

        return $text;
    }

    public function line($text){
        if(!defined('STDOUT')){
            return;
        }
        fwrite(\STDOUT, $text.PHP_EOL);
    }

    public function print($text){
        if(!defined('STDOUT')){
            return;
        }
        fwrite(\STDOUT, $text);
    }

    public function error($text){
        $error = $this->colorize_string('Error:', 'red');
        $this->line($error .' '. $text);
        die();
    }

    public function success($text){
        $success = $this->colorize_string('Success:', 'green');
        $this->line($success.' '.$text);
        die();
    }
}