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
//echo 'Currently logged in as: '.$_SESSION["username"];
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
	
	textarea
	{
		position: absolute;
		top: 445px;
		resize: none;
	}
	
	
	body 
	{
		//background-color: #006699;
		background-color: lightgray;
		//color: 	#F0FFF0;	
		color: navy;
	
	}
	
	.padding
	{
		margin-left: 30px;
	}
	
	.paramLabel
	{
		padding-right: 20px;
	}
 
   table
   {
	 background-color:black;
	 border: 10px double white;
     width: 800px;
	 height: auto;
	 color: black;
     position: absolute;
     left: 350px;
     top: 100px; 
   }
 
   table, th, td
   {
     
     border-collapse: collapse;
   }
   
   .Table_Header
   {
	   position:fixed;
	   top: 0;
   }
   
   th, td
   {  
	 border: 1px solid black;
     padding: 5px;
     text-align: center;
   }
   
   table tbody
   {
		display: block;
		width: 100%;
		height: 600px;
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
  
  .box
  {  
    position: absolute;
	margin-bottom: 300px;
  
  }
  
  .logoutLblPos
  {

   position:absolute;
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
   .button:hover
   {
     background: #3B5C76;
  
   }
	
	</style>
	</head>
	
	<body>
	
	<nav>
		<ul>
			<li><a href="teacherAdd.php" class="active">Create Questions/Exams</a></li>
			<li><a href="teacherCreate.php">Release Tests/Scores</a></li>
		</ul>
	</nav>
 
   <form align="right" name="form1" method="post" action="logOut.php">
    <label class="logoutLblPos">
      <input name="Logout" type="submit" id="submit2" value="log out">
    </label>
   </form>
	
	<div style="width:100%">
	<form name="questionForm" action="submitQuestion.php" method="post">
	
		<div>
		<p style="position:absolute; top:410px"><b><i>Enter problem description here:</b></i> </p>
			<textarea name="description" placeholder="Enter some text..." rows="7" cols="40" required></textarea>
		</div>
		
		<br>
		
		<div style="position:absolute; top:50px">
			<p><b><i>Select a return type: </b></i></p>
			<select name="returnType" id="selectReturn" >
				<option value="none">Void</option>
				<option value="int">Int</option>
				<option value="float">Float</option>
				<option value="boolean">Boolean</option>
				<option value="String">String</option>
			</select>
		</div>
		
		
		
		<div style="position:absolute; top:140px">
			<b><i>Please enter parameters here (if any):</b></i> <br>
			
			<span class="paramLabel"> Param Type:</span>	
			<span> Param Name: </span>
			<br>
			<select name="paramType1" id="firstParamType" onChange="changeTextBox1();">
				<option value="none">none</option>
				<option value="int">Int</option>
				<option value="float">Float</option>
				<option value="boolean">Boolean</option>
				<option value="String">String</option>
			</select>
			<input type="text" name="name1" id="nameBox1" class="padding" disabled>
			<br>
			
			<script>
				function changeTextBox1()
				{
					
					var str = document.getElementById("firstParamType").value;
					
					if(str != "none")
					{
						document.getElementById("nameBox1").disabled = false;
			
					}
					else
					{
						document.getElementById("nameBox1").disabled = true;
					}
					
				}
			
		
			</script>
			
			<span class="paramLabel"> Param Type:</span>	
			<span> Param Name: </span>
			<br>
			<select name="paramType2" id="secondParamType" onChange="changeTextBox2();">
				<option value="none">none</option>
				<option value="int">Int</option>
				<option value="float">Float</option>
				<option value="boolean">Boolean</option>
				<option value="String">String</option>
			</select>
			<input type="text" name="name2" id="nameBox2" class="padding" disabled>
			<br>
			
			<script>
				function changeTextBox2()
				{
					
					var str = document.getElementById("secondParamType").value;
					
					if(str != "none")
					{
						document.getElementById("nameBox2").disabled = false;
			
					}
					else
					{
						document.getElementById("nameBox2").disabled = true;
					}
					
				}
			
		
			</script>
			
			<span class="paramLabel"> Param Type:</span>	
			<span> Param Name: </span>
			<br>
			<select name="paramType3" id="thirdParamType" onChange="changeTextBox3();">
				<option value="none">none</option>
				<option value="int">Int</option>
				<option value="float">Float</option>
				<option value="boolean">Boolean</option>
				<option value="String">String</option>
			</select>
			<input type="text" name="name3" id="nameBox3" class="padding" disabled>
			<br>
			
			<script>
				function changeTextBox3()
				{
					
					var str = document.getElementById("thirdParamType").value;
					
					if(str != "none")
					{
						document.getElementById("nameBox3").disabled = false;
			
					}
					else
					{
						document.getElementById("nameBox3").disabled = true;
					}
					
				}
			
		
			</script>
			
			<span class="paramLabel"> Param Type:</span>	
			<span> Param Name: </span>
			<br>
			<select name="paramType4" id="fourthParamType" onChange="changeTextBox4();">
				<option value="none">none</option>
				<option value="int">Int</option>
				<option value="float">Float</option>
				<option value="boolean">Boolean</option>
				<option value="String">String</option>
			</select>
			<input type="text" name="name4" id="nameBox4" class="padding" disabled>
			<br>
			
			<script>
				function changeTextBox4()
				{
					
					var str = document.getElementById("fourthParamType").value;
					
					if(str != "none")
					{
						document.getElementById("nameBox4").disabled = false;
			
					}
					else
					{
						document.getElementById("nameBox4").disabled = true;
					}
					
				}
			
		
			</script>
			
		
		</div>
		
		<div style="position:absolute; top:330px">
			<b><i>Name of Function:</b></i> <br>
			<input type="text" name="funcName" required>	
		</div>
		
		<div style="position:absolute; top:375px">
			<b><i>Number of Points Worth:</b></i> <br>
			<input type="text" name="pointValue" required>
		
		</div>
		
		
		<div style="position:absolute; top:560px">
			<b><i>Constraints:</b></i><br>
			<input type="checkbox" name="constraint1" value="if/else"> If/Else <br>
			<input type="checkbox" name="constraint2" value="for loop"> For Loop <br>
			<input type="checkbox" name="constraint3" value="while loop"> While Loop <br>
		</div>
		
		<div style="position:absolute; top:650px">
			<b><i>Difficulty:</b></i><br>
			<select name="Difficulty" >
				<option value="easy">Easy</option>
				<option value="medium">Medium</option>
				<option value="hard">Hard</option>
			</select>
			
		</div>
				
		
		<input class="button" type="submit" value="Submit Question" style="position:absolute; top:705px">
   
   </form>
   </div>
   
   <div style="width:50%">
   <form name="testForm" action="submitTest.php" method="post"> 
   
   
   <div id="box" >

   <?php
	require 'request.php';
	if(!isset($_POST["Submit_Filter"]))
	{
		$type = "getQuestionBank";
		$j_response = request($type, " ", "https://web.njit.edu/~dyp6/CS490/MidToBack.php");
	}
	else
	{
		$arr = array("filter" => $_POST["filter"]);
		
		$type = "filterQuestionBank";
		$j_response = request($type, $arr, "https://web.njit.edu/~dyp6/CS490/MidToBack.php");
		
	}
   
     $add = 'Add';
     $func = 'Func Name';
     $point = 'Point Value';
     $descr = 'Description';
     $return = 'Return Type';
     $paramType = 'Parameter Types';
     $paramName = 'Parameter Names';
     $constraints = 'Constraints';
	 $difficulty = 'Difficulty';
     
     //print_r($j_response);
   
     echo '<table id="questionTable">';
		
	   //echo '<div style="position:absolute; top: 0;>';	
       echo '<tr id="Table_Header">';
       echo '<th>'.$return.'</th>';
       echo '<th>'.$func.'</th>';
       echo '<th>'.$descr.'</th>';
       echo '<th>'.$point.'</th>';
	   echo '<th>'.$difficulty.'</th>';
       echo '<th>'.$paramType.'</th>';
       echo '<th>'.$paramName.'</th>';
       echo '<th>'.$constraints.'</th>';
       echo '<th>'.$add.'</th>';
       echo '</tr>';
	   //echo '</div>';
     
     foreach ($j_response as $key => $value)
     {
       //echo $key;
       echo '<tr>';
       $count = 0;
       $store = '';
       foreach ($value as $element)
       {
          echo '<td>'.$element.'</td>';
          if($count == 1)
            $store = $element;
          $count++;
       }
       echo '<td> <input type="checkbox" name="question[]" value='.$store.'> </td>';
       echo '</tr>';
     }
     
     echo '</table>';
     ?>
     
   </div>
   
   <div style="position:absolute; top:730px; left:570px; ">
			<b><i>Name of Test: </b></i><input type="text" name="testName" size="35" required>
	</div>
   
   <input class="button" type="submit" value="Submit Test" style="position:absolute; top:770px; left:720px; ">
  
  </form>
  
  <form method="post" action="#">
	  <div style="position:absolute; left:350px;">
	  <b><i>Filter: </b></i> 
	  <input type="radio" name="filter" value="Text" > Description &nbsp;
	  <input type="radio" name="filter" value="Points"> Point Value &nbsp;
	  <input type="radio" name="filter" value="returnType"> Return Type &nbsp;
	  <input type="radio" name="filter" value="functionName"> Func Name &nbsp;
	  <input type="radio" name="filter" value="Difficulty"> Difficulty
	  </div>
  
	<input class="button" name="Submit Filter" type="submit" value="Submit Filter" style="position:absolute;left:1000px">
  
  </form>
  </div>
  
   
   
	
	
	</body>





</html>