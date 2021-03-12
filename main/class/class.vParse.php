<?php

class vParse{
	public $cur_version = false;
	
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
				return 'greater';
			}else if($first[$check] < $second[$check]){
				return 'lesser';
			}
		}
		return 'equal';
	}
	
	public function parse($string){
		$lifecycles = [
			'a' => 0, //Alpha
			'b' => 1, //Beta
			'rc' => 2 //Release Candidate
		];
		$ver_data = [
			'lifecycle' => '',
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
}