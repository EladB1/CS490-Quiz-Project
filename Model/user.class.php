<?php
  include 'singleton.class.php';
  class user{ //May help to make this abstract then inherit student and admin from it
    private $UCID;
    private $passwd;
    private $role; //admin or student
    private $loginAttempt; //true or false
    public function authenticate($userName, $password){
      //check if the login credentials match those in the database
      $dbHandle = singleton::connectToDB();
      $query = 'SELECT UserID, Password, Role FROM CS490_Users WHERE UserID =
      :userName AND Password = :password';
      $execute = $dbHandle->prepare($query);
      $execute->bindParam(':userName', $userName);
      $execute->bindParam(':password', $password);
      $execute->execute();
      $execute->setFetchMode(PDO::FETCH_ASSOC); //put the results in an associative array
      $results = $execute->fetchAll();
      if(isset($results)){
      	$this->UCID = $results['UserID'];
	$this->passwd = $results['Password'];
	$this->role = $results['Role'];
	$this->loginAttempt = true;
	$this->save($this->UCID, $this->role);
	json_encode($this); //put user information into a JSON object
      }
      else{
      	$this->loginAttempt = false;
      	json_encode($this);
      }
      return $this->loginAttempt;
    }
    public function save($userName, $Role){ //store user information in the session variable(on the server)
      session_start();
      $_SESSION['UCID'] = $userName;
      //$_SESSION['Role'] = $Role;
    }
    public function logout(){
      session_start();
      session_unset($_SESSION['UCID']);
      header('web.njit.edu/~dt242'); //redirect to the login page
    }
  }
?>
