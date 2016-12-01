<?php
session_start(); 
require 'request.php';
$testName = $_POST["attemptCheck"][0];
$sendName = array('ExamName' => $testName);
$request = 'checkAttempts';
  
$sendData = array("examName" => $testName, "userID" => $_SESSION["username"] );
$validResponse = request($request, $sendData, "https://web.njit.edu/~dyp6/CS490/MidToBack.php");
//var_dump($validResponse);
$validResponse = (array)$validResponse;
if($validResponse[0] != "Allowed")
{
  echo "<script>alert('You cannot attempt the test more than once!'); window.location = 'studentView.php';</script>";
  exit();
}
if($testName == NULL && !isset($_POST["Submit_Test"]) )
{
	echo "<script>alert('Please select a test first!'); window.location = 'studentView.php';</script>";
    exit();
}
$type = 'seeExam';
$j_response = request($type,$sendName,"https://web.njit.edu/~dyp6/CS490/MidToBack.php" );
   
echo '<form name="testAnswers" action="" method="post">';
$count = 1;
foreach($j_response as $question)
{
	echo '<input type="hidden" name ="test" value='.$testName.'>';
	
	$funcName;
	$prompt;
	$value;
	$return;
	$p_type;
	$p_name;
	$constraints;
	$tab = "\t";
	
	
	
	foreach($question as $key => $element)
	{
		if($key == "Text")
		{
			//echo '<br>'.$element;
			$prompt = $element;
			
		}
		else if($key == "functionName")
		{
			/*echo 'Question '.$count;
			echo '<br>Function Name: '.$element;
			echo '<br><br><textarea name="description[]" value="answer" style="resize:none" placeholder="Enter answer here" rows="7" cols="50"></textarea>';
			echo '<br><br>';
			$count += 1;*/
			$funcName = $element;
		}
		else if($key == "Points")
			$value = $element;
		else if($key == "returnType")
			$return = $element;
		else if($key == "Parameter types")
			$p_type = $element;
		else if($key == "Parameter names")
			$p_name = $element;
		  
		
	}
	$p_typeArray = explode(",", $p_type);
	$p_nameArray = explode(",", $p_name);
	
	//var_dump($p_type);
	//var_dump($p_name);
	//var_dump($p_typeArray);
	//var_dump($p_nameArray);
	
	if($value == 1)
		echo 'Question '.$count.': '.$value.' point';
	else
		echo 'Question '.$count.': '.$value.' points';
	echo '<br>'.$prompt;
	echo '<br>Function Name: '.$funcName.'<br>';
	
	if($p_typeArray[0] != "")
	{
		for($i = 0; $i < count($p_typeArray); $i++)
		{
			echo "Param Type " . ($i+1) . ": ".$p_typeArray[$i];
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "Param Name ". ($i+1) .": ".$p_nameArray[$i]."<br>";
		}
	}
	
	echo '<br><br><textarea name="description[]" value="answer" style="resize:none" placeholder="Enter answer here" rows="7" cols="50"></textarea>';
	echo '<br><br>';
	
	$count++;
	
	
	
}
echo '<input class="button" name="Submit Test"  type="submit" value="submit Test">';
echo '</form>';
//print_r($_POST);
//REQUEST STUFF HERE
function stripTags($array)
{
  foreach($array as $element)
  {
         $element = htmlspecialchars($element, ENT_QUOTES, 'UTF-8');
  }
         
  return $array;
          
}
if(isset($_POST["Submit_Test"]))
{
  
  $arr = stripTags($_POST);
  
  
  $sendMsg = array("user" => $_SESSION["username"], "questions" => $arr);
  requestTest($sendMsg,"https://web.njit.edu/~dyp6/CS490/GradeAnswer.php" );
	
  header("Location: studentView.php");
  exit();
	
	
}
//echo "Hello!"
	
echo '<style>';

echo 'body 
	{
		//background-color: #006699;
		background-color: lightgray;
		//color: 	#F0FFF0;	
		color: navy;
	
	}
	.button
	{
		width: 140px;
		text-align: center;

		display:block;
		
		padding:2px 4px;
		font-family:helvetica;
		font-size:16px;
		font-weight:100;
		color:#fff;
		background: #587286;
		border:0;
		font-weight:100;

	}
	';

echo '</style>';

?>


	
	
  
  
  
 