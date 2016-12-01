<?php
	session_start();
	require 'request.php';
	
	if(!isset($_SESSION["username"]))
	{
		header("Location: http://afsaccess2.njit.edu/~dt242");
		exit();
	}
	if($_SESSION["role"] != "Student")
	{
		header("Location: http://afsaccess2.njit.edu/~dt242/logOut.php");
		exit();
	}
	
	$type = "seeFeedback";
	
	$data = array("userID" => $_SESSION["username"], "ExamName" => $_POST["nameArray"][0]);
	
	$j_response = request($type, $data, "https://web.njit.edu/~dyp6/CS490/MidToBack.php");
	
	//var_dump($j_response);
	
	$string = '
	
		<html lang="en-US">
  <head>
  <style>
	div
	{
		width: 500px;
		word-break: break-all;

	}

  </style>
  </head>
  <body>
  ';
  
  $count = 1;
  
  foreach ($j_response as $var)
  {
	  $funcName;
	  $text;
	  $pointsWorth;
	  $return;
	  $paramTypes;
	  $paramNames;
	  $constraints;
	  $feedback;
	  $pointsEarned;
	  $correctReturn;
	  $correctResult;
	  
	  foreach ($var as $key => $value)
	  {
		  if($key == "functionName")
		  {
			  $funcName = $value;
		  }
		  else if ($key == "Text")
		  {
			  $text = $value;
		  }
      else if ($key == "Points")
        $pointsWorth = $value;
      else if ($key == "returnType")
        $return = $value;
      else if ($key == "Parameter types")
        $paramTypes = $value;
      else if ($key == "Parameter names")
        $paramNames = $value;
      else if ($key == "Constraints")
        $constraints = $value;
      else if ($key == "FeedBack")
        $feedback = $value;
      else if ($key == "pointsReceived")
        $pointsEarned = $value;
      else if ($key == "Correct returnType")
        $correctReturn = $value;
      else
        $correctResult = $value;
		  
		  
	  }
     
    //---CODE THAT DISPLAYS CONTENT OF EACH QUESTION---
    $string .= '  
			<div style="background-color:black;color:white;padding:20px;">
        Exam: '.$_POST["nameArray"][0].'
				<h1>Question '.$count.':</h1>
        '.$text.'
			</div>
      
			<div style="padding:20px;">
				<h2>Function Name: '.$funcName.'<br>
        Return Type: '.$return.'<br>
        Parameter Types: '.$paramTypes.'<br>
        Parameter Names: '.$paramNames.'<br>';
        if($constraints == "")
          $string .='Constraints    : None <br>';
        else
          $string .='Constraints    : '.$constraints.'<br>';
        $string .='</h2>
       </div>';
       
       $string .= '
       <div style="background-color:grey;padding:20px;">
		      <h2>Result:</h2>
          <p>
            Points Possible: '.$pointsWorth.'<br>
            Points Earned  : '.$pointsEarned.'<br>
          </p>
	     </div>';
      
       $string .= '
       <div style="background-color:#55676A;padding:20px;">
		      <h2>Comments:</h2>
          <p>
            '.$feedback.'
          </p>
	     </div>
       <br><br>';      
	  
	  $count++;
  }//end foreach loop
  
  $string .= '
  <a href="studentScores.php">Back</a>';

  /*
	<div style="background-color:black;color:white;padding:20px;">
		<h1>Question 1</h1>
	</div>

	<div style="padding:20px;">
		<h2>The question:</h2>
		<p>TextTextTextTextTextTextTextTextText</p>
	</div>

	<div style="padding:20px;">
		<h2>Your answer:</h2>
		<p style="width :50%;">TextTextTextTextTextTextTextTextTextTextTextTextTextTextTextTextTextTextTextTextText</p>
	</div>

	<div style="padding:20px;">
		<h2>Comments:</h2>
		<p>TextTextTextTextTextTextTextTextText</p>
	</div>



	Question 1<br>
	This is a question



	Question 1<br>
	This is a question



	Question 1<br>
	This is a question


	Question 1<br>
	This is a question


  </body>


  </html>
  ';*/
  
  echo $string;
	




?>