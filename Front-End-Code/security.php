<?php session_start(); 
$_SESSION["username"] = $_POST["username"];
$_SESSION["password"] = $_POST["password"];

//<?php
require 'request.php';

//Daniel Thomas 
//Dhruv Patel 
//Elad Bergrin
//FRONT END CODE - PHP BACKEND SCRIPT

$user = $_POST["username"]; 
$pass = $_POST["password"];

$info = array("username" => $user,"password" => $pass );//puts user and pass info into an array
$type = "login";

//Sends type and info to request.php 
$j_response = request($type,$info, "https://web.njit.edu/~dyp6/CS490/MidEndLogin.php" );

//print_r($j_response);

if (!$j_response["loginAttempt"])
{
  //echo "Invalid login information. Please try again";
  echo "<script>alert('Invalid username or password. Please try again.'); window.location = 'http://afsaccess2.njit.edu/~dt242';</script>";
}
else
{
  if($j_response["Role"] === "Student"){
    $_SESSION["role"] = "Student";
    header("Location: http://afsaccess2.njit.edu/~dt242/studentView.php");
	//exit();
  }
  else{
    $_SESSION["role"] = "Teacher";
    header("Location: http://afsaccess2.njit.edu/~dt242/teacherAdd.php");
	//echo "Teacher logged in";
	//exit();
  }
}



?>



