<?php
session_start(); 

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
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
	<style>
	  ul
	  {
		  list-style-type: none;
		  margin: 0;
		  padding: 0;
		  overflow: hidden;
		  background-color: black;
	
	  }
	
	  li
	  {
		  float: left;
		  border-right: 1px solid white;
	  }
	
	  li:last-child
	  {
		  border-right: none;
	  } 
	
	  li a
	  {
		  display: block;
		  color: white;
		  text-align: center;
		  padding: 14px 16px;
		  text-decoration: none;
	  }
	
	  li a:hover:not(.active)
	  {
		  background-color: blue;
	  }
	  .active
	  {
		  background-color: red;
	  }
	
	  body 
	{
		//background-color: #006699;
		background-color: lightgray;
		//color: 	#F0FFF0;	
		color: navy;
	
	}
 
    table
     {
       width: 100%;
       position: absolute;
       left: 20px;
       top:100px;
     }
 
     table, th, td
     {
       border: 1px solid black;
       border-collapse: collapse;
       border-bottom: 1px solid #ddd;
	   width: 400px;
     }
 
     th, td
     {
       padding: 5px;
       text-align: center;
     }
   
     table tbody
     {
		  display: block;
		  height: 400px;
		  overflow: auto;

     }
 
     table#questionTable tr:nth-child(even) 
    {
      background-color: #eee;
    }
    table#questionTable tr:nth-child(odd) 
    {
      background-color:#fff;
    }
    table#questionTable th 
    {
      background-color: black;
      color: white;
    }
    
    box
    {
      position: absolute;
  
    }
  
    .logoutLblPos
    {

     position:fixed;
     right:20px;
     top:20px;
    }
	
	.button
	{
		width: 140px;
		text-align: center;
		margin:0 auto;
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
	<nav>
		<ul>
			<li><a href="studentView.php" >View Tests</a></li>
			<li><a href="studentScores.php"class="active" >Review Scores</a></li>
		</ul>
	</nav>
 
   <form align="right" name="form1" method="post" action="logOut.php">
    <label class="logoutLblPos">
      <input name="Logout" type="submit" id="submit2" value="log out">
    </label>
   </form>
   
   <form name="testForm" action="testFeedback.php" method="post"> 
 
 <?php
	session_start();
	require 'request.php';
		
	$type = "seeGrade";
	//$userSend = array('userID' => $_SESSION["username"]);
	$j_response = request($type, $_SESSION["username"] , "https://web.njit.edu/~dyp6/CS490/MidToBack.php");
	//var_dump($j_response);
	//var_dump($userSend);
	//var_dump($_SESSION["username"]);
   
     
     $name = 'Test Name';
	 $score = 'Points Earned';
	 $total = 'Points Total';
	 $view = 'View Score';
	 
     
     if($j_response[0] == "Score not available for viewing.")
       echo '<h1>Nothing to display yet.</h1>';
     else
     {  
        echo '<table id="questionTable">';
     
        echo '<tr>';
        echo '<th>'.$name.'</th>';
	      echo '<th>'.$score.'</th>';
	      echo '<th>'.$total.'</th>';
        echo '<th>'.$view.'</th>';
        echo '</tr>';
       
    
        foreach ($j_response as $key => $value)
        {
          echo '<tr>';
          $count = 0;
          $store = '';
          foreach ($value as $element)
          {
             echo '<td>'.$element.'</td>';
             if($count == 0)
               $store = $element;
             $count++;
          }
          echo '<td> <input type="radio" name="nameArray[]" value='.$store.'> </td>';
          echo '</tr>';
        }
     
         echo '</table>';
      }
     
   
     
  
  if($j_response[0] != "Score not available for viewing.")
       echo '<input type="submit" class="button" value="View Score" style="position:absolute; top:510px; left:10%; ">';
  
  ?>
  
  </form>
  
  
  <!-- MAKE REQUEST HERE FOR GETTING GRADED RESULT -->
   
   
   
   </body>
   
</html>
   
   
   
   
   
   
   
   