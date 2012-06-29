<?php

/* Possible params
 * $lang - Device language
 * $manufacturer - Device manufacturer
 * $device - device model name
 * $uuid - client side generated unique user id
 * $country - device country
 * $version - app version
 */
$lang;
$manufacturer;
$device;
$uuid;
$country;
$version;


setGetParams($_GET);

/**
  * Return the bucket number of the user. There a 100 possible buckes.
  */  
function getUserBucket($uuid) {
	$lastChars = substr($uuid, -5);
	
	$segmentId = $lastChars[0];
	
	for($i = 1; $i < strlen($lastChars); $i++){
		$segmentId = ($segmentId << 3) + ord($lastChars[$i]);
	}
	
	$segment = substr($segmentId, -2);
	
	return $segment;
}

function renderResultJson($resultArray) {
	header('Content-type: application/json');
	echo json_encode($resultArray);
}

function setGetParams($appParams) {
	global $lang, $manufacturer, $device, $uuid, $country, $version;
	
	$uuid = getUserBucket($appParams, 'uuid');
	$device = getUserBucket($appParams, 'device');
	$manufacturer = getUserBucket($appParams, 'manufacturer');
	$lang = getUserBucket($appParams, 'lang');
	$version = getUserBucket($appParams, 'version');
	$country = getUserBucket($appParams, 'country');
}

function getArrayParam($array, $paramName){
	if(isset($array[$paramName])) 
		return $array[$paramName];
	else
		return "";
}

function isInBucket($uuid, $low, $high) {
	$userBucket = getUserBucket($uuid);
	if ($userBucket >= $low && $userBucket < $high) 
		return true;
	else
		return false;
}

function activeExperimentReturnArray($values = null) {
	return array('isActive' => true,
				'values' => $values);		
}

function inactiveExperimentReturnArray() {
	return array('isActive' => false,
				'values' => null);		
}

//returns boolean result array without any values if user is in bucket.
//$low has to be <= $high
function turnOnBucket($uuid, $low, $high) {
	if(empty($uuid)) 
		return inactiveExperimentReturnArray();
	
	//define buckets for experiment
	if(isInBucket($uuid, $low, $high))
		return activeExperimentReturnArray();
	else
		return inactiveExperimentReturnArray();
}
?>