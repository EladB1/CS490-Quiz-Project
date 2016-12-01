<?php

function exit_session($username, $role)
{
	session_start();
    session_unset($username);
	session_unset($role);
  
    session_destroy();
  
    header("Location: http://afsaccess2.njit.edu/~dt242");
    exit();
}
session_start();

exit_session($_SESSION["username"], $_SESSION["role"]);
//print_r($_SESSION);




?>