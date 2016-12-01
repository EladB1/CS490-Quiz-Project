<?php
session_start();
require 'request.php';

$checked = $_POST["question"];
$testName = $_POST["testName"];

$arrSend = array();

if(!empty($_POST['question']))
{
  foreach($_POST['question'] as $check)
  {
    $arrSend[] = $check;
  
  }

}
//else if (empty($_POST['question']))
  //echo "empty!";
  
$type = "addExam";

//MAKE ASSOCIATIVE ARRAY AND USE REQUEST TO SEND IT

$msg = array("ExamName" => $testName, "Questions" => $arrSend);

$j_response = request($type, $msg, "https://web.njit.edu/~dyp6/CS490/MidToBack.php" );

//echo json_encode($j_response);

//$custom_message = "Test successfully added!";



/*<script type="text/javascript">
  var alertMsg = '<?php echo $custom_message; ?>';
  alert(alertMsg);
</script>*/

echo "<script>alert('Test successfully added!'); window.location = 'teacherAdd.php';</script>";
exit();


//echo $jsMsg;

//header("Location: teacherAdd.php");


?>