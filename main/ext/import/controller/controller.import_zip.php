<?php
die('Wrong File');
$u_dir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
$filename = $u_dir . 'zip_code_database.csv';
$h = fopen($filename, 'r');
/*
Name: Stephen Kennedy
Date: 5/12/2020
Comment: Post Office Zipcode Database
*/
$lines = 0;
$fieldKey = [];
while(($data = fgetcsv($h, 1000, ",")) !== FALSE){
	if($lines == 0){
		foreach($data as $key => $value){
			$fieldKey[$value] = $key; //This is both to make the code more readable, and to allow us to modify the CSV and have the import still work without worrying about column #
		}
	}else{
		$zip = $data[$fieldKey['zip']];
		$city = $data[$fieldKey['primary_city']];
		$add_cities = $data[$fieldKey['acceptable_cities']];
		$state = $data[$fieldKey['state']];
		$county = $data[$fieldKey['county']];
		
		$sql = 'INSERT into `zipcode` (zip_code, zip_city, zip_add_cities, zip_county, zip_state) VALUES (:zip, :city, :add_cities, :state, :county)';
		$params = [
			':zip' => $zip,
			':city' => $city,
			':add_cities' => $add_cities,
			':state' => $state,
			':county' => $county
		];
		$db->query($sql, $params);
	}	
	$lines++;
}
$lines--;
echo $lines . ' zipcodes entered into database';