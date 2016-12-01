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
  
  $count = 1;
  $totalEarned = 0;
  $totalPossible = 0;
  
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
	  $code;
	  
	  
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
	  {
        $pointsWorth = $value;
		$totalPossible += $value;
	  }
      else if ($key == "returnType")
        $return = $value;
	  else if ($key == "Code")
		$code = $value;  
      else if ($key == "Parameter types")
        $paramTypes = $value;
      else if ($key == "Parameter names")
        $paramNames = $value;
      else if ($key == "Constraints")
        $constraints = $value;
      else if ($key == "FeedBack")
        $feedback = $value;
      else if ($key == "pointsReceived")
	  {
        $pointsEarned = $value;
		$totalEarned += $value;
	  }
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
        <font size="5"><i>'.$text.'</i></font>
      
		<h2>Function Name: '.$funcName.'<br>
        Return Type: '.$return.'<br>';
		if($paramTypes == "")
		{
			$string .= 'Parameter Types: None <br>';
			$string .= 'Parameter Names: None <br>';
        
		}
		else
		{
			
			$string .= 'Parameter Types: '.$paramTypes.'<br>';
			$string .= 'Parameter Names: '.$paramNames.'<br>';
		}
        if($constraints == "")
          $string .='Constraints    : None <br>';
        else
          $string .='Constraints    : '.$constraints.'<br>';
        $string .='</h2>
       </div>';
	   $code = nl2br($code);
	   $string .='
	   <div style="background-color:white;color:black;padding:20px;">
	   <h2>Your Code:</h2>
	   <p>
			<pre>'.$code.'</pre>
	   </p>
	   </div>
	   
	   
	   ';
	   
	   $string .= '
       <div style="background-color:black;color:white;padding:20px;">
		      <h2>Comments:</h2>
          <p>
            '.$feedback.'
          </p>
	     </div>'
       ;  
       
       $string .= '
       <div style="background-color:white;color:black;padding:20px;">
		      <h2>Result:</h2>
          <p>
            Points Possible: '.$pointsWorth.'<br>
            Points Earned  : '.$pointsEarned.'<br>
          </p>
	     </div>
		 <br><br>';
      
           
	  
	  $count++;
  }//end foreach loop
  
  $string .='
	<div style="background-color:white;color:red;padding:20px;">
	<h1>
	Final Grade: '.$totalEarned.'/'.$totalPossible.'
	</h1>
	</div>';
  
  /*$string .= '
  <a href="studentScores.php">Back</a>';*/
  $string .= '<br><input class=button1 value=Back type=button onclick="location=\'studentScores.php\'">';


  
  echo $string;
	




?>