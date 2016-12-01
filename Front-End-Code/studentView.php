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
		  background-color:black;
		  border: 10px double white;
		  text-align: center;
		  width: 50%;
		  height: 50%;
		  border=0;
		  cellspacing = 0;
		  cellpadding = 0;
		  position: absolute;
		  margin-left:auto;
		  margin-right:auto;
		  padding:0;
		  margin:0;
		  //left: 350px;
		  top:100px; 
     }
 
     table, th, td
     {
		padding:0;
		margin:0;	 
       //border: 1px solid black;
       border-collapse: collapse;
       //border-bottom: 1px solid #ddd;
	   //width: 700px;
	   width: 100%;
	   height: 48%;
     }
 
     th, td
     {
       //padding: 5px;
	   padding:0;
	   margin:0;
       text-align: center;
	   width: 100%;
	   height: 100%;
     }
   
     table tbody
     {
		  background-color:lightgray;
		  padding:0;
		  margin:0;
		  display: block;
		  //height: 400px;
		  overflow: auto;
		  width: 100%;
		  height: 100%;

     }
	 
	 
 
     table#questionTable tr:nth-child(even) 
    {
      background-color: #eee;
	  padding:0;
	  margin:0;
	  
    }
    table#questionTable tr:nth-child(odd) 
    {
      background-color:#fff;
	  padding:0;
	  margin:0;
    }
    table#questionTable th 
    {
      background-color: black;
      color: white;
	  padding:0;
	  margin:0;
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
		//margin:0 auto;
		display:block;
		text-align:center;
		
		
		padding:1px 3px;
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
			<li><a href="studentView.php"class="active" >View Tests</a></li>
			<li><a href="studentScores.php" >Review Scores</a></li>
		</ul>
	</nav>
 
   <form align="right" name="form1" method="post" action="logOut.php">
    <label class="logoutLblPos">
      <input name="Logout" type="submit" id="submit2" value="log out">
    </label>
   </form>
   
   <form name="testForm" action="takeTest.php" method="post"> 
 
    <?php
		require 'request.php';
		
		$type = "studentExamList";
		$j_response = request($type, " ", "https://web.njit.edu/~dyp6/CS490/MidToBack.php");
   
     
		 $name = 'Test Name';
	   $attempts = 'Attempts Left';
	   $takeTest = 'Take Test';
     
		echo '<table id="questionTable">';
     
       echo '<tr>';
       echo '<th>'.$name.'</th>';
	   //echo '<th>'.$attempts.'</th>';
	   echo '<th>'.$takeTest.'</th>';
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
	   //CHECK HERE IF STORE IS 0, IF IT IS THAT MEANS ATTEMPTS IS 0 AND THEY CANT TAKE
       echo '<td> <input type="radio" name="attemptCheck[]" value='.$store.'> </td>';
	   //echo '<td> <input type="radio" name="Take Test!" value='.$store.'style="position:relative; "> </td>';
       echo '</tr>';
     }
     
      echo '</table>';
     
   
  
  echo '<input type="submit" class="button" value="Take Test!" style="position:absolute; top:550px; left:50%; ">';
  
  ?>
  
  </form>
 
	</body>
 
 </html