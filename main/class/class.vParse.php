<?php

class vParse{
	public $cur_version = false;
	public $diff = false;
	
	public function __construct(){
		$this->cur_version = VER;
	}
	
	public function greater($first, $second = false){
		if(empty($second)){
			$second = $this->cur_version;
		}
		$check = $this->compare($first, $second);
		switch($check){
			case 'greater':
				return true;
				break;
			default:
				return false;
		}
	}
	
	public function compare($first, $second = false){
		if(empty($second)){
			$second = $this->cur_version;
		}
		$first = $this->parse($first);
		$second = $this->parse($second);
		
		$loops = [
			'lifecycle',
			'major',
			'minor',
			'hotfix'
		];
		
		foreach($loops as $check){
			if($first[$check] > $second[$check]){
				$this->diff = $check;
				return 'greater';
			}else if($first[$check] < $second[$check]){
				$this->diff = $check;
				return 'lesser';
			}
		}
		return 'equal';
	}
	
	public function parse($string){
		$lifecycles = [
			'a' => 0, //Alpha
			'b' => 1, //Beta
		];
		$ver_data = [
			'lifecycle' => 2,
			'major' => 0,
			'minor' => 0,
			'hotfix' => 0
		];
		foreach($lifecycles as $check => $value){
			$temp = substr($string, (-1 * strlen($check)));
			if($temp == $check){
				$ver_data['lifecycle'] = $value;
				$string = substr($string, 0, (strlen($string) - strlen($check)));
			}
		}
		$array = explode('.', $string);
		$ver_data['major'] = $array[0];
		if(isset($array[1])){
			$ver_data['minor'] = $array[1];
		}
		if(isset($array[2])){
			$ver_data['hotfix'] = $array[2];
		}
		return $ver_data;
	}
	
	public function increment($level, $ver = false){
		if(empty($ver)){
			$ver = $this->cur_version;
		}
		$ver = $this->parse($ver);
		switch($level){
			case 'lifecycle':
				if($ver['lifecycle'] < 2){
					$ver['lifecycle']++;
					if($ver['lifecycle'] == 2){
						$ver['major'] = 1;
						$ver['minor'] = 0;
					}else{
						$ver['major'] = 0;
						$ver['minor'] = 1;
					}
					$ver['hotfix'] = 0;
				}
				break;
			case 'major':
				$ver['major']++;
				$ver['minor'] = 0;
				$ver['hotfix'] = 0;
				break;
			case 'minor':
				$ver['minor']++;
				$ver['hotfix'] = 0;
				break;
			case 'hotfix':
				$ver['hotfix']++;
				break;
		}
		return $this->rebuild($ver);
	}
	
	public function rebuild($ver_object){
		$string = $ver_object['major'];
		if(!empty($ver_object['minor'])){
			$string .= '.'.$ver_object['minor'];
		}
		if(!empty($ver_object['hotfix'])){
			$string .= '.'.$ver_object['hotfix'];
		}
		if($ver_object['lifecycle'] != 2){
			$key = [
				'a',
				'b',
			];
			$string .= $key[$ver_object['lifecycle']];
		}
		return $string;
	}
}