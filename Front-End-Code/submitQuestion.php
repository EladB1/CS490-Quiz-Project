<?php
session_start();
require 'request.php';
//strings
$desc = $_POST["description"];
$points = $_POST["pointValue"];
$return = $_POST["returnType"];
$funcName = $_POST["funcName"];
$difficulty = $_POST["Difficulty"];

//arrays
$paramType = array($_POST["paramType1"], $_POST["paramType2"], $_POST["paramType3"], $_POST["paramType4"]);
$paramName = array($_POST["name1"], $_POST["name2"], $_POST["name3"], $_POST["name4"]);

$paramCombined = array();

for($i = 0; $i < count($paramType); $i++)
{
  if($paramType[$i] != "none" && !$paramName[$i].trim() == "" ){ 
     array_push($paramCombined, $paramType[$i] . " " . $paramName[$i]);
  }

}

$constraints = array($_POST["constraint1"], $_POST["constraint2"], $_POST["constraint3"]);

//echo $desc, $points, $return, $funcName, implode(" ",$paramCombined), implode(" ",$constraints);

$msg = array("description" => $desc,"points" => $points,"return" => $return,"functionName" => $funcName,
"Difficulty" => $difficulty,"parameters" => $paramCombined,"constraints" => $constraints);

$type = "addToQuestionBank";

//$sendMsg = array("RequestType" => $type, "Data" => $msg);

$j_response = request($type, $msg, "https://web.njit.edu/~dyp6/CS490/MidToBack.php" );

//echo json_encode($j_response);


echo "<script>alert('Question successfully added!'); window.location = 'teacherAdd.php';</script>";
exit();

?>

