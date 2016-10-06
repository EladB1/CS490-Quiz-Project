<?php
/*Elad Bergrin*/
/*Group Members:
  Daniel Thomas
  Dhruv Patel
*/
  abstract class model{
    protected $dbHandle;
    public function connectToDB(){
      $dbPass = '********';
      try{
        $this->dbHandle = new PDO('mysql:host=sql2.njit.edu; dbname=eb86', 'eb86', $dbPass);
	    $dbObject = $this->dbHandle;
	    return $dbObject;
      }
      catch(PDOException $excp){
        return 'Database connection problem: ' . $excp->getMessage() ."\n";
	
      }
    }
  }
  class user extends model{
    private $loggedInUsers;
    public function __construct(){
      $this->loggedInUsers = 0;
    }
    public function authenticate($userName, $password){
      try{
        $PDO_Obj = $this->connectToDB();
        $query = 'SELECT Role FROM CS490_Users WHERE UserID = :username AND Password = :password LIMIT 1'; //limit 1 will make it stop after getting result
	$exec = $PDO_Obj->prepare($query);
        $exec->bindParam(':username', $userName);
        $exec->bindParam(':password', $password);
        $exec->execute();
        $queryResult = $exec->fetchAll(PDO::FETCH_ASSOC); //get an associative array from the result
        $results = array('UserID' => '', 'Role' => '', 'loginAttempt' => '');
        $results['UserID'] = $userName;
	if(empty($queryResult)){ //nothing was returned by the database
	  $results['Role'] = ' ';
	  $results['loginAttempt'] = false; //bad login attempt
	}
	else if(!empty($queryResult)){ 
	  $results['Role'] = $queryResult[0]['Role'];
	  $results['loginAttempt'] = true;
	}
	$JSON = json_encode($results);
	return $JSON;
      }
      catch(PDOException $excp){
        return 'Database error: ' . $excp->getMessage() . "\n";
      }
    }
    public function login(){
	  $message = json_decode(file_get_contents('php://input'), true); //get request from middle end
	  $user = $this->authenticate($message['username'], $message['password']);
	  $this->loggedInUsers += 1;
	  session_start();
	  $_SESSION[$this->loggedInUsers]['user'] = $user['UserID'];
	  $_SESSION[$this->loggedInUsers]['type'] = $user['Role'];
	  return $user;
    }
    public function logout($username, $type){
      session_start();
      session_unset($this->loggedInUsers);
      $this->loggedInUsers -= 1;
    }
  }
  abstract class userBase extends user{ //user class handles login and userBase handles after the login
    private $username;
    public function __construct($username){
      $this->username = username;
    }
  }
  class student extends userBase{
    public function takeTest(){
    
    }
    public function seeScore(){
    
    }
  }
  class admin extends userBase{
    public function makeTest(){
    
    }
    public function releaseScores(){
    
    }
    public function makeQuestion(){
      //write a question and add to the question bank
    }
  }
  class question extends model{
    //public $questionBank;
    private $points;
    private $difficulty;
    private $parameters;
    private $returnType;
    private $functionName;
    private $TestCases;
    public function __construct($points, $difficulty){ //user creates a question
      $this->points = $points;
      $this->difficulty = $difficulty;
    }
    public function addToQuestionBank($question){ //create new question in database
    
    }
    public function editQuestion(){
    
    }
  }
  class exam extends model{
    private $examQuestions;
    private $questionBank; //questions stored in database
    private $totalPoints;
    public function __construct(){
      $dbHandle = $this->connectToDB();
      $examQuestions = array();
      $questionBank = array();

      //load all of the questions in the database into the questionBank array, encode it wih JSON, and send it to the middle end
    }
    public function addQuestion($question){
    
    }
    public function removeQuestion($question){
    
    }
  }
  function main(){
    $user = new user();
    $arr = $user->login();
    if($arr['Role'] == 'Student'){
      $user = new student($arr['UserID']);
    }
    if($arr['Role'] == 'Admin'){
      $user = new admin($arr['UserID']);
    }
    print_r($arr); //print the json encoded result so that the middle-end can receive a response to its curl request
    //print_r($_SESSION);
  }
  main();
?>
