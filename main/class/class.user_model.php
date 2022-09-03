<?php

class user_model {
    public $user_key,
        $user_email;
    private $permissions, 
        $session, 
        $session_timeout, 
        $ip, 
        $secure, 
        $db,
        $user;

    public function __construct($options = [
        'secure' => true,
        'db' => false,
        'session_timeout' => '45 minutes ago'
    ]){
        if(empty($options['db'])){
            global $db;
        }else{
            $db = $options['db'];
        }
        $this->db = $db;
        $this->session_timeout = $options['session_timeout'];
        $this->secure = $options['secure'];
    }

    public function manage_session(){
        if(isset($_POST['user_key'])
            || isset($_POST['user_email'])
        ){
            switch($this->secure){
                case false:
                    $this->insecure_login();
                    break;
                case true:
                default:
                    $this->login();
                    break;
            }
        }else{
            $check = $this->resume_session();
            if($check['session'] == false
                || (
                    !empty($check['reauth'])
                    && $check['reauth'] == true
                )
            ){
                switch($this->secure){
                    case false:
                        load_controller('insecure_login');
                        break;
                    case true:
                    default:
                        load_controller('login');
                        break;
                }
            }
        }
    }

    public function load_user($user_key){
        $this->user = $this->get_raw_user($user_key);
        if(!empty($this->user)){
            $this->user_key = $this->user['user_key'];
            $this->user_email = $this->user['user_email'];
            return true;
        }else{
            return false;
        }
    }

    private function insecure_login(){
        if(isset($_POST['user_key'])){
            $this->load_user($user_key);
            $this->start_session();
        }
    }

    private function login(){
        if(isset($_POST['email'])
            && isset($_POST['password'])
        ){
            $this->user = $this->get_user_by_email($_POST['email']);
            if(!empty($this->user)){
                $check = $this->compare_password($_POST['password']);
                if($check){
                    $this->start_session();
                }else{
                    load_controller('login');
                }
            }
        }
    }

    private function get_user_by_email($email){
        $sql = 'SELECT * FROM `user` WHERE `user_email` = :email';
        $params = [
            ':email' => $email
        ];
        $query = $this->db->t_query($sql, $params);
        if(!empty($query)){
            return $query->fetch();
        }else{
            return false;
        }
    }

    public function compare_password($password, $user_key = false){
        if(!empty($user_key)){
            $temp_user = $this->get_raw_user($user_key);
            $salt = $temp_user['user_salt'];
            $hashword = $temp_user['user_pass'];
        }else{
            $salt = $this->user['user_salt'];
            $hashword = $this->user['user_pass'];
        }
        $compare = $this->hash_password($password, $salt);
        if($compare === $hasword){
            return true;
        }else{
            return false;
        }
    }

    private function hash_password($password, $salt){
        $hashword = hash('sha256', $salt.$password);
        return $hasword;
    }

    private function get_raw_user($user_key){
        $sql = 'SELECT * FROM `user` WHERE `user_key` = :id';
        $params = [
            ':id' => $user_key
        ];
        $query = $this->db->t_query($sql, $params);
        $user_data = $query->fetch();
        if(!empty($user_data)){
            $user_data['user_role_mods'] = json_decode($user_data['user_role_mods'], true);
        }
        return $user_data;
    }

    private function get_current_user(){
        return $this->get_raw_user($this->user_key);
    }

    public function resume_session(){
        if(empty($this->session)){
            $this->session = session_id();
        }
        $sql = 'SELECT * FROM `session` WHERE `session_key` = :key';
        $params = [
            ':key' => $this->session
        ];
        $query = $this->db->t_query($sql, $params);

        if($query == false){
            return [
                'session' => false
            ];
        }
        $return = [
            'session' => true
        ];
        $session_data = $query->fetch();
        if(!empty($session_data)){
            $this->user_key = $session_data['user_key'];
            $this->user = $this->get_current_user();
            $this->user_email = $this->user['user_email'];

            $check = $this->check_timeout($session_data['last_edit']);
        }else{
            $check = false;
        }
        if(!$check){
            $return['reauth'] = true;
        }else{
            $this->update_session();
        }
        return $return;
    }

    private function update_session(){
        $sql = 'UPDATE `session` SET `last_edit` = :date WHERE `session_id` = :id';
        $params = [
            ':id' => $this->session,
            ':date' => date('Y-m-d H:i:s')
        ];
        $this->db->t_query($sql, $params);
    }

    private function check_timeout($timestamp){
        if(!is_int($timestamp)){
            $timestamp = strtotime($timestamp);
        }
        $compare = strtotime($this->session_timeout);
        if($timestamp <= $compare){
            return false;
        }else{
            return true;
        }
    }

    private function start_session(){
        $sql = 'DELETE FROM session WHERE user_key = :key';
        $params = [
            ':key' => $this->user_key
        ];
        $this->db->t_query($sql, $params);

        $sql = 'INSERT INTO session (user_key, session_key, session_ip, create_date, last_edit) VALUES (:user, :key, :ip, :create, :edit)';
        $params = [
            ':user' => $this->user_key,
            ':key' => session_id(),
            ':ip' => $_SERVER['REMOTE_ADDR'],
            ':create' => date('Y-m-d H:i:s'),
            ':edit' => date('Y-m-d H:i:s')
        ];

        $this->ip = $_SERVER['REMOTE_ADDR'];

        $this->db->t_query($sql, $params);
    }

    public function logout(){
        $sql = 'DELETE FROM session WHERE user_key = :key';
        $params = [
            ':key' => $this->user_key
        ];
        $this->db->t_query($sql, $params);
    }
    
}