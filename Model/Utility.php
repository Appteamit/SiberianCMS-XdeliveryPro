<?php

class Xdelivery_Model_Utility extends Core_Model_Default {

 	// static public function displayPrice($price, $currency, $decimals = 2, $decimalpoint = '.', $seperator = ',', $currency_positions = 'left') {

 	// 	if($currency_positions == 'left'){
 	// 		return $currency.''.number_format(floor(($price * pow(10, $decimals))) / pow(10, $decimals), $decimals, $decimalpoint, $seperator);
 	// 	}
 	// 	if($currency_positions == 'left_with_space'){
 	// 		return $currency.' '.number_format(floor(($price * pow(10, $decimals))) / pow(10, $decimals), $decimals, $decimalpoint, $seperator);
 	// 	}
 	// 	if($currency_positions == 'right'){
 	// 		return number_format(floor(($price * pow(10, $decimals))) / pow(10, $decimals), $decimals, $decimalpoint, $seperator).''.$currency;
 	// 	}
 	// 	if($currency_positions == 'right_with_space'){
 	// 		return number_format(floor(($price * pow(10, $decimals))) / pow(10, $decimals), $decimals, $decimalpoint, $seperator).' '.$currency;;
 	// 	}
        
    //     return number_format(floor(($price * pow(10, $decimals))) / pow(10, $decimals), $decimals, $decimalpoint, $seperator);

    // }

	static public function displayPrice($price, $currency, $decimals = 2, $decimalpoint = '.', $seperator = ',', $currency_positions = 'left') {
		if ($decimals>2) {
			$decimals=2; //Always set display decimals upto 2
		}
		
		$formattedPrice = number_format(round($price, $decimals), $decimals, $decimalpoint, $seperator);
	
		if ($currency_positions == 'left') {
			return $currency . $formattedPrice;
		}
		if ($currency_positions == 'left_with_space') {
			return $currency . ' ' . $formattedPrice;
		}
		if ($currency_positions == 'right') {
			return $formattedPrice . $currency;
		}
		if ($currency_positions == 'right_with_space') {
			return $formattedPrice . ' ' . $currency;
		}
	
		return $formattedPrice;
	}
	
    public static function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2) {
	 
	    $theta = $lon1 - $lon2;
	    $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
	    $miles = acos($miles);
	    $miles = rad2deg($miles);
	    $miles = $miles * 60 * 1.1515;
	    $feet = $miles * 5280;
	    $yards = $feet / 3;
	    $kilometers = $miles * 1.609344;	    
	    $meters = $kilometers * 1000;

	    return compact('miles','feet','yards','kilometers','meters');
	}

	function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'k') {
 
		$theta = $longitude1 - $longitude2;
		$dist = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}

}