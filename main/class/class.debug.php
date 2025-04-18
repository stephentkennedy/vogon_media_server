<?php
namespace vogon;

/**
 * This is an enforced singleton class designed to provide debugging tools when needed.
 * 
 * It provides simple methods for dumping variables, asserting comparisons (in a non-exception driven way), and managing timers.
 * 
 * To disable debug output, call the disable() method. It will not output anything after that point, there is no method to re-enable. The intent is to remove all debug output in production situations, even if debug code was not fully removed.
 */
class debug{

    private $disable = false;

    private $mode = 'browser';

    private static $instance = null;

    private function __construct(){
        $this->data_store = new \data_store;
    }

    private $data_store = null;

    public static function get_instance(){
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function disable(){
        $this->disabled = true;
        return true;
    }

    public function set_mode($mode){
        $allowed = [
            'browser',
            'js_console',
            'cli',
            'error_log'
        ];

        if(in_array($mode, $allowed)){
            $this->mode = $mode;
            return true;
        }
        return false;
    }

    public function dump($var){
        if($this->disabled){
            return;
        }
        $mode = $this->mode;
        $method = $mode.'_dump';
        if(method_exists($this, $method)){
            $this->{$method}($var);
        }else{
            //Fall back on error dump if the wrong mode was used
            $this->error_dump($var);
        }
    }

    /**
     * Asserts that a comparison is true, otherwise debugs in the current debug mode.
     * 
     * This differs from the native PHP assert in that it does not throw an exception, this is only useful for informative debugging.
     * 
     * @param mixed $val1 The first value to compare.
     * @param string $compare The comparison operator to use.
     * @param mixed $val2 The second value to compare
     * 
     * @return null
     */
    public function assert($val1, $compare, $val2){
        $valid_compares = [
            '==',
            '===',
            '!=',
            '!==',
            '>',
            '>=',
            '<',
            '<=',
            '<=>',
            'instance_of'
        ];

        if(in_array($compare, $valid_compares)){
            //Compare val1 and val2
            $return = false;
            switch($compare){
                case '==':
                    $return = ($val1 == $val2);
                    break;
                case '===':
                    $return = ($val1 === $val2);
                    break;
                case '!=':
                    $return = ($val1 != $val2);
                    break;
                case '!==':
                    $return = ($val1 !== $val2);
                    break;
                case '>':
                    $return = ($val1 > $val2);
                    break;
                case '>=':
                    $return = ($val1 >= $val2);
                    break;
                case '<':
                    $return = ($val1 < $val2);
                    break;
                case '<=':
                    $return = ($val1 <= $val2);
                    break;
                case '<=>':
                    $return = ($val1 <=> $val2);
                    break;
                case 'instance_of':
                    //Check if val1 is instance of val2
                    $return = is_a($val1, $val2);
                    break;
            }
            if(!$return){
                $this->debug([
                    'Assertion Failed',
                    'val1' => $val1,
                    'compare' => $compare,
                    'val2' => $val2
                ]);
            }
        }else{
            $this->debug([
                'Invalid Compare Passed to Assertion',
                'val1' => $val1,
                'compare' => $compare,
                'val2' => $val2
            ]);
        }
    }

    public function start_timer($name){
        $timer_name = $name . '_timer';
        $this->data_store->set($timer_name, microtime());
        $this->debug($name.' timer started.');
    }

    public function check_timer($name, $precision){
        $timer_name = $name . '_timer';
        $passed_time = $this->get_time_passed($timer_name);
        $passed_time = $this->time_to_precision($passed_time, $precision);
        $this->debug($name . ' timer value: '.$passed_time .' '. $precision);
    }

    public function stop_timer($name, $precision = 'seconds'){
        $passed_time = $this->check_timer($name, $precision);
        $this->remove_timer($name);
        $this->debug($name. ' timer stopped.');
    }

    private function remove_timer($name){
        $timer_name = $name . '_timer';
        $this->data_store->unset($timer_name);
    }
    
    private function get_time_passed($timer_name){
        $current_time = microtime();
        $passed = $this->data_store->get($timer_name);
        return $current_time - $passed;
    }

    private function time_to_precision($microtime_value, $precision){
        switch($precision){
            case 'seconds':
                //Convert microseconds to seconds
                $to_return = $microtime_value / 1000000;
                break;
            case 'milliseconds':
                //Convert microseconds to milliseconds
                $to_return = $microtime_value / 1000;
                break;
            case 'microseconds':
            default: //Default fallthrough
                $to_return = $microtime_value;
                break;

        }
        return $to_return;
    }

    private function browser_dump($var){
        echo '<pre class="debug-dump">';
        var_dump($var);
        echo '</pre>';
    }

    private function js_console_dump($var){
        echo '<script type="text/javascript">console.log(' . json_encode($var) . ');</script>';
    }

    private function cli_dump($var){
        if(
            class_exists('cli')
            || (
                function_exists('load_class')
                && load_class('cli')
            )
        ){
            $cli = new cli;
            $prefix = $cli->colorize_string('DEBUG:', 'yellow');
            $cli->line($prefix);
            $cli->line(json_encode($var, JSON_PRETTY_PRINT));
        }else{
            echo 'DEBUG:'.PHP_EOL;
            echo json_encode($var, JSON_PRETTY_PRINT);
            echo PHP_EOL;
        }
    }

    private function error_log_dump($var){
        error_log(PHP_EOL.'DEBUG:'.PHP_EOL.json_encode($var, JSON_PRETTY_PRINT).PHP_EOL);
    }
}