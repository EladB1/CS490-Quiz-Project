<html>
<body>

<?php
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

if($j_response["NJITValid"])
{
  echo "You cannot use actual NJIT login information!";
}
else if (!$j_response["loginAttempt"])
{
  echo "Invalid login information. Please try again";
}
else
{
  if($j_response["Role"] === "Student")
    echo "Welcome Student!";
  else
    header("Location: http://afsaccess2.njit.edu/~dt242/teacherAdd.html");
}



?>

</body>
</html>

