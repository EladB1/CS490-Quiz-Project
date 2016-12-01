<?php
session_start();
function request($type, $data, $destination)
{
	$sendMsg = array("RequestType" => $type, "Data" => $data);
	
	$json_data = json_encode($sendMsg);
	
	$ch = curl_init();
	//sends the info
	curl_setopt($ch, CURLOPT_URL, $destination);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	//receives the info
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$headers= array('Accept: application/json','Content-Type: application/json'); 
	curl_setopt($Curl_Mid, CURLOPT_HTTPHEADER, $headers);
	//receive response from Mid End

	$response = curl_exec($ch); //stores output of url into variable

	curl_close($ch); //closes the connection 

	unset($ch);
	
	$j_response = json_decode($response, true);
	
	return $j_response;
	
}

function requestTest($data, $destination)
{
	
	$json_data = json_encode($data);
	
	$ch = curl_init();
	//sends the info
	curl_setopt($ch, CURLOPT_URL, $destination);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	//receives the info
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$headers= array('Accept: application/json','Content-Type: application/json'); 
	curl_setopt($Curl_Mid, CURLOPT_HTTPHEADER, $headers);
	//receive response from Mid End

	$response = curl_exec($ch); //stores output of url into variable
	
	echo curl_getinfo($ch).'<br>';
	echo curl_errno($ch).'<br>';
	echo curl_error($ch).'<br>';

	curl_close($ch); //closes the connection 

	unset($ch);
	
	//$j_response = json_decode($response, true);
	
	
	
	return $response;
	
	
	
	
	
}


?>