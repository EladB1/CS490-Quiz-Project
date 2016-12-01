<?php
session_start();
require 'request.php';

$releaseTest = $_POST["testArray"];
$releaseScore = $_POST["scoreArray"];

//$arrSend = array();

//var_dump($releaseTest);
//var_dump($releaseScore);

/*if(!empty($_POST['question']))
{
  foreach($_POST['question'] as $check)
  {
    $arrSend[] = $check;
  
  }

}
else if (empty($_POST['question']))
  echo "empty!";
  
//MAKE ASSOCIATIVE ARRAY AND USE REQUEST TO SEND IT*/

$type1 = "activateExam";
$type2 = "releaseGrades";

//var_dump($releaseTest);


$msg1 = array("ExamName" => $releaseTest[0]);
$msg2 = array( "ExamName" => $releaseScore[0]);


if($releaseTest != NULL){
  $j_response1 = request($type1, $msg1, "https://web.njit.edu/~dyp6/CS490/MidToBack.php" );
  //echo json_encode($j_response1);
}
if($releaseScore != NULL){
  $j_response2 = request($type2, $msg2, "https://web.njit.edu/~dyp6/CS490/MidToBack.php" );
  //echo json_encode($j_response2);
}
/*echo "<script>";
echo "alert('Test released!')";
echo "</script>";*/


header('Location: teacherCreate.php');
exit();






?>