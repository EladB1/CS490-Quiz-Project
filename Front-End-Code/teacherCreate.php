<?php
session_start();
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
	   border: 10px double white;
	   background-color:black;
       width: 120%;
       position: absolute;
       left: 100px;
       top: 100px; 
     }
 
     table, th, td
     {
       //border: 1px solid black;
       border-collapse: collapse;
       //border-bottom: 1px solid #ddd;
	     width: 700px;
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
		  background-color:lightgray;

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

     position:absolute;
     right:20px;
     top:20px;
    }
	
	.button
	{
		width: 200px;
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
	
	</style>
	</head>
	
	
	<body>
	<nav>
		<ul>
			<li><a href="teacherAdd.php" >Create Questions/Exams</a></li>
			<li><a href="teacherCreate.php" class="active">Release Tests/Scores</a></li>
		</ul>
	</nav>
 
   <form align="right" name="form1" method="post" action="logOut.php">
    <label class="logoutLblPos">
      <input name="Logout" type="submit" id="submit2" value="log out">
    </label>
   </form>
   
<form style="text-align:center" name="testForm" action="releaseScores.php" method="post"> 
 
 <?php
		require 'request.php';
		
		$type = "adminExamList";
		$j_response = request($type, " ", "https://web.njit.edu/~dyp6/CS490/MidToBack.php");
   
     
     $name = 'Test Name';
     $release = 'Released?';
     $active = 'Active?';
     $graded = 'Graded?';
     $optionTest = 'Release Test';
     $optionScore = 'Release Score';
     
     echo '<table id="questionTable">';
     
       echo '<tr>';
       echo '<th>'.$name.'</th>';
       echo '<th>'.$active.'</th>';
       echo '<th>'.$graded.'</th>';
       echo '<th>'.$release.'</th>';
       echo '<th>'.$optionTest.'</th>';
       echo '<th>'.$optionScore.'</th>';
       echo '</tr>';
       
     
     foreach ($j_response as $key => $value)
     {
       echo '<tr>';
       $count = 0;
       $store = '';
       foreach ($value as $element)
       {
		  //var_dump($element); 
		  if($count == 0)
		  {
			echo '<td><a href="teacherDisplay.php?view='.$element.'">'.$element.'</a></td>';	
			$store = $element;
		  }
		  else	
			echo '<td>'.$element.'</td>';
			  
          $count++;
       }
       echo '<td> <input type="radio" name="testArray[]" value='.$store.'> </td>';
       echo '<td> <input type="radio" name="scoreArray[]" value='.$store.'> </td>';
       echo '</tr>';
     }
	
      echo '</table>';
	  
	  //var_dump($j_response);
     
   
     
  ?>
  
  <input type="submit" class="button" value="Release Tests & Scores" style="position:absolute; top:550px; left:330px; ">
  
  </form>
 
	</body>
 
 </html
	
	
	
	