<?php

class data_store{
    private $data = [];

    private static $instance = null;

    public static function get_instance(){
        if(self::$instance === null){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function set($key, $data){
        $this->data[$key] = $data;
    }

    public function get($key, $default = null){
        if(!isset($this->data[$key])){
            return $default;
        }
        return $this->data[$key];
    }
}