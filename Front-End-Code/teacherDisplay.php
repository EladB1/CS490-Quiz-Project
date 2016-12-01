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
//print_r($j_response);

/*if(isset($_POST)){ //check if form was submitted
	header("Location:studentView.php;" );

}*/

   
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
			$funcName = $element;
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
	
	echo '<br><br>';
	
	$count++;
	
}
	
	




?>