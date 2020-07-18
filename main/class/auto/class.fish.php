<?php
/*
Name: Stephen Kennedy
Date: 11/7/18
Comment: This is the Babel Fish, or Fish for short. Named after the plot convenience from Douglas Adam's Hitchhiker's Guide, it's job is to allow us to communicate with the outside world via the CURL library, with limited fallback on file(http://) if CURL is not enabled.
*/
class fish{
	public $httpFlag = false;
	public $get;
	public $post;
	public $send;
	public $raw;
	public $parsed;
	public $headers = [];
	public $options = [
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_FOLLOWLOCATION => TRUE,
		CURLOPT_AUTOREFERER => TRUE,
		CURLOPT_CONNECTTIMEOUT => 443,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_SSL_VERIFYPEER => 0, //Almost never useful, so it defaults to off
		CURLOPT_SSL_VERIFYHOST => 0
	];
	public $url;
	public $info;
	
	public function __construct(){
		if(function_exists('curl_init')){
			$this->httpFlag = true;
		}
	}
	
	//Send Data Translation functions
	public function toJson($data = false, $source = 'post'){
		if($data === false){
			$data = $this->{$source};
		}
		$data = json_encode($data);
		if($data === false){
			return false;
		}else{
			$this->{$source} = $data;
			return true;
		}
	}
	
	public function toXml($data = false, $source = 'post'){
		if($data === false){
			$data = $this->{$source};
		}
		$data = $this->recursiveXml($data);
		//Don't have a good way to handle errors, should fix that
		$this->{$source} = $data;
		return true;
	}
	
	private function recursiveXml($array){
		$return = '';
		foreach($array as $key => $value){
			$return .= '<'.$key.'>';
			if(gettype($value) == 'array' || gettype($value) == 'object'){
				$return .= $this->recursiveXml($value);
			}else{
				$return .= $value;
			}
			$return .= '</'.$key.'>';
		}
		return $return;
	}
	
	//CURL functions
	public function cHead($array = [], $append = false){
		if(gettype($array) == 'string'){
			$array = [$array];
		}
		if($append == false){
			$this->headers = $array;
		}else{
			$this->headers = array_merge($this->headers, $array);
		}
	}
	
	public function cOps($array = [], $append = false){
		if(gettype($array) != 'array'){
			die('fish->cOps() can only accept options as an associative array of option indentifying constant and option value.');
		}
		if($append == false){
			$this->options = $array;
		}else{
			$this->options = array_merge($this->options, $array);
		}
	}
	
	public function cDNS($ip){
		$this->options[CURLOPT_DNS_SERVERS] = $ip;
	}
	
	public function cUpload($field, $file){
		$this->post[$field] = new CurlFile($file, mime_content_type($file));
	}
	
	public function curl(){
		$this->options[CURLOPT_URL] = $this->url;
		$ch = curl_init();
		if(!empty($this->post) && count($this->post) > 0){
			$this->options[CURLOPT_POST] = true;
			$this->options[CURLOPT_POSTFIELDS] = $this->post;
		}
		if(!empty($this->get) && count($this->get) > 0){
			$string = '';
			foreach($this->get as $key => $value){
				$string .= urlencode($key).'='.urlencode($value).'&';
			}
			$string = rtrim($string, '&');
			$this->options[CURLOPT_URL] .= '?'.$string;
		}
		curl_setopt_array($ch, $this->options);
		$result = curl_exec($ch);
		if(curl_error($ch)){
			$this->info = curl_error($ch);
		}else{
			$this->info = curl_getinfo($ch);
		}
		curl_close($ch);
		return $result;
	}
	
	//fopen based curl fallback
	public function curl_fallback(){
		$url = $this->url;
		if(!empty( $this->get) && count($this->get) > 0){
			$string = '';
			foreach($this->get as $key => $value){
				$string .= urlencode($key).'='.urlencode($value).'&';
			}
			$string = rtrim($string, '&');
			$url .= '?'.$string;
		}
		$returned = file_get_contents($url);
		return $returned;
	}
	
	//Main data retrieval function
	public function dispatch(){
		if($this->httpFlag == true){
			$data = $this->curl();
		}else{
			$data = $this->curl_fallback();
		}
		$this->raw = $data;
	}
	
	//Returned Data Translation functions
	public function import($data){
		$this->raw = $data;
		$this->parsed = false;
	}
	
	public function json($data = false){
		if($data == false){
			$data = $this->raw;
		}else{
			$this->import($data);
		}
		$data = json_decode($data, true);
		$this->parsed = $data;
		if($data == false){
			return false;
		}else{
			return true;
		}
	}
	
	public function xml($data = false){
		//This is a fun hack, we can json encode an object, and then json decode it into an associative array, so we'll load the data into a simple xml parser first
		//The bad news is doing it this way will lose us the properties provided, if any, which is why we keep the raw on hand and publicly accessable.
		if($data == false){
			$data = $this->raw;
		}else{
			$this->import($data);
		}
		$xml = simplexml_load_string($data);
		$xml = json_encode($xml);
		return $this->json($xml);
	}
	
	//You called the babel fish as a string, so we'll just give you whatever data we have stored instead of erroring out like those less well behaved classes
	public function __toString(){
		return '<pre>'.var_dump($this->parsed).'</pre>';
	}
	
	//You called the babel fish and invoked it as a function. We'll assume that means you want to import some data and place it in our raw data prop, or if you don't provide any data, we'll give you what we have parsed already.
	public function __invoke($data = false){
		if($data !== false){
			$this->load($data);
		}else{
			return $this->parsed;
		}
	}
	
	//You dumped the babel fish, rude.
	public function __debugInfo(){
		return 'This is the Babel Fish. It&#39;s supposed to facilitate inter-device communication via CURL, but you&#39;ve gone and dumped it on the floor.';
	}
	
	
}
?>