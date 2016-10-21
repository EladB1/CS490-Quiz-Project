<?php
require 'request.php';
//strings
$desc = $_POST["description"];
$points = $_POST["pointValue"];
$return = $_POST["returnType"];
$funcName = $_POST["funcName"];

//arrays
$paramType = array($_POST["paramType1"], $_POST["paramType2"], $_POST["paramType3"], $_POST["paramType4"]);
$paramName = array($_POST["name1"], $_POST["name2"], $_POST["name3"], $_POST["name4"]);

$paramCombined = array();

for($i = 0; $i < count($paramType); $i++)
{
  if($paramType[$i] != "none" && !$paramName[$i].trim() == "" ){ 
	 //echo $paramType[$i];
     array_push($paramCombined, $paramType[$i] . " " . $paramName[$i]);
  }

}

$constraints = array($_POST["constraint1"], $_POST["constraint2"], $_POST["constraint3"], $_POST["constraint4"], $_POST["constraint5"]);

//echo $desc, $points, $return, $funcName, implode(" ",$paramCombined), implode(" ",$constraints);

$msg = array("description" => $desc,"points" => $points,"return" => $return,"functionName" => $funcName,
"parameters" => $paramCombined,"constraints" => $constraints);

$type = "addToQuestionBank";

$sendMsg = array("RequestType" => $type, "Data" => $msg);

/*
$json_data = json_encode($sendMsg);

echo $json_data;

$ch = curl_init();
//sends the info
curl_setopt($ch, CURLOPT_URL, "https://web.njit.edu/~dyp6/CS490/CreateQuestion.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

//receives the info
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//$headers= array('Accept: application/json','Content-Type: application/json'); 
curl_setopt($Curl_Mid, CURLOPT_HTTPHEADER, array (
	'query: Submit Question'
	));
 //receive response from Mid End

$response = curl_exec($ch); //stores output of url into variable

curl_close($ch); //closes the connection 

unset($ch);			*/


$j_response = request($type, $msg, "https://web.njit.edu/~dyp6/CS490/MidToBack.php" );

echo $j_response;


?>