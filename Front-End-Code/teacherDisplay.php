<?php
	session_start();
	require 'request.php';
	
	if(!isset($_SESSION["username"]))
	{
		header("Location: http://afsaccess2.njit.edu/~dt242");
		exit();
	}
	if($_SESSION["role"] != "Teacher")
	{
		header("Location: http://afsaccess2.njit.edu/~dt242/logOut.php");
		exit();
	}
	
$type = 'seeExam';


$sendName = array("ExamName" => $_GET["view"]);


$j_response = request($type,$sendName,"https://web.njit.edu/~dyp6/CS490/MidToBack.php" );
//var_dump($j_response);

/*if(isset($_POST)){ //check if form was submitted
	header("Location:studentView.php;" );

}*/

$string = '
	<html lang="en-US">
  <head>
  <style>
	div
	{
		width: 500px;
		word-break: break-all;

	}
	body 
	{
		//background-color: #006699;
		background-color: lightgray;
		//color: 	#F0FFF0;	
		color: navy;
	
	}
	
	.button1
	  {
		width: 140px;
		//margin:0 auto;
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

  </style>
  </head>
  <body>
';

   
$string .= '<form name="testAnswers" action="" method="post">';

$count = 1;
$totalPoints = 0;

foreach($j_response as $question)
{
	$string .= '<input type="hidden" name ="test" value='.$testName.'>';
	
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
			$funcName = $element;
		else if($key == "Points")
		{
			$value = $element;
			$totalPoints += $element;
		}
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
		$string .= 'Question '.$count.': '.$value.' point';
	else
		$string .= 'Question '.$count.': '.$value.' points';
	$string .= '<br>'.$prompt;
	$string .= '<br>Function Name: '.$funcName.'<br>';
	
	if($p_typeArray[0] != "")
	{
		for($i = 0; $i < count($p_typeArray); $i++)
		{
			$string .= "Param Type " . ($i+1) . ": ".$p_typeArray[$i];
			$string .= "&nbsp;&nbsp;&nbsp;&nbsp;";
			$string .= "Param Name ". ($i+1) .": ".$p_nameArray[$i]."<br>";
		}
	}
	
	$string .= '<br><br>';
	
	$count++;
	
}

$string .="Total Points: ".$totalPoints;

$string .= '<br><br><input class=button1 value=Back type=button onclick="location=\'teacherCreate.php\'">';

echo $string;
	
	




?>