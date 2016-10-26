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
	$this->dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbObject = $this->dbHandle;
	return $dbObject;
      }
      catch(PDOException $excp){
        return 'Database connection problem: ' . $excp->getMessage() ."\n";
	
      }
    }
    public static function handle_curl_Request(){
      //take in data as JSON array that has the request type and any associated data
      //format should be associative array encoded into JSON in the form of array('RequestType' => 'RequestName', 'Data' => 'your data')
      $request =(array) json_decode(file_get_contents('php://input', true));
      //print_r($request);
      switch($request['RequestType']){
        case 'login':
	  //do something
	  $user = new user();
	  $login = $user->login((array)$request['Data']);
	  print_r($login);
	  break;
        case 'getQuestionBank':
	  //do something 
	  $ex = new exam();
	  $ex->getQuestionBank();
	  break;
	case 'addExam':
	  //do something
	  $ex = new exam();
          $ex->newExam((string)$request['Data']['ExamName'], (array) $request['Data']['Questions']); //questions will be added in here
	  break;
	case 'addToQuestionBank':
	  //do something
	  $question = new question();
	  $insert = $question->addToQuestionBank((array)$request['Data']);
	  //var_dump($insert);
	  print_r(json_encode((array)$insert));
	  break;
	case 'adminExamList':
	  //do something
	  $query = 'SELECT ExamName, ExamStatus, GradingStatus, scores FROM Exam';
          $list =(array) userbase::getExamList($query);
	  print_r(json_encode($list));
	  break;
	case 'studentExamList':
	  //do something
	  $query = 'SELECT ExamName FROM Exam';
	  $list = (array)userbase::getExamList($query);
	  print_r(json_encode($list));
	  break;
	case 'seeExam':
	  //do something
	  break;
	case 'releaseGrades':
	  //do something
	  break;
	case 'seeGrade':
	  //do something
	  break;
	default:
	   $message = '400: HTTP Request not recognized';
	   $response = array($message);
	   print_r(json_encode($response));
	  break;
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
    public function login($message){
	  $user = $this->authenticate($message['username'], $message['password']);
	  $this->loggedInUsers += 1;
	  /*
	  session_start();
	  $_SESSION[$this->loggedInUsers]['user'] = $user['UserID'];
	  $_SESSION[$this->loggedInUsers]['type'] = $user['Role'];
	  */
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
    public static function getExamList($str){ //pass in a string to differentiate between the student exam list and admin exam lists; as of now neither are user dependent, but that may change
      $dbHandle = $self->connectToDB();
      $stmt = $dbHandle->prepare($str);
      $stmt->execute();
      $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $exams;
    }
  }
  class student extends userBase{
    public function takeTest(){
    
    }
    public function seeScore(){
    
    }
  }
  class admin extends userBase{
    /*public function makeTest(){
    
    }*/
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
    /*public function __construct($points, $difficulty){ //user creates a question
      $this->points = $points;
      $this->difficulty = $difficulty;
    }*/
	public function fixAutoIncrements($ParamID, $QuestID){
		try{
		  $query1 = 'ALTER TABLE Parameters AUTO_INCREMENT = ' . $ParamID;
		  $query2 = 'ALTER TABLE Questions AUTO_INCREMENT = ' . $QuestID;
		  $this->dbHandle->query($query1);
		  $this->dbHandle->query($query2);
		}
		catch(PDOException $excp){
			echo 'Fix auto_increment error ' . $excp->getMessage();
		}
	}
	public function getMaxID($tableName, $idColName){
	  $dbHandle = $this->connectToDB();
	  $query = 'SELECT MAX(' . $idColName . ') FROM ' . $tableName;
	  $res = $dbHandle->prepare($query);
	  $res->execute();
          $result = $res->fetchAll(PDO::FETCH_ASSOC);
	  return $result[0]['MAX('.$idColName.')'];
	}
	public function getNumberofRows($tableName){
		try{
		  $dbHandle = $this->connectToDB();
		  $query = 'SELECT COUNT(*) FROM '. $tableName; //doesn't allow you to use table name as parameter
		  $exec = $dbHandle->prepare($query);
		  $exec->execute();
		  $res = $exec->fetchAll(PDO::FETCH_ASSOC);
		  return $res[0]['COUNT(*)'];
		}
		catch(PDOException $excp){
			echo 'Number of questions error: ' . $excp->getMessage();
		}
	}
    public function addToQuestionBank($arr){ //create new question in database
		//$currPID = $this->getNumberofRows('Parameters'); //auto_increment not reset after rollback, could break logic
		//$currQID = $this->getNumberofRows('Questions');
		$currPID = $this->getMaxID('Parameters', 'paramID'); //auto_increment not reset after rollback, could break logic
		$currQID = $this->getMaxID('Questions', 'QuestionID');
                //var_dump($currPID);
		try{
		  $dbHandle = $this->dbHandle;
		  $trans = $dbHandle->beginTransaction();
		  //$dbHandle->query('SET SQL_MODE = \'STRICT_ALL_TABLES\''); //no values not in specified in database field with enum declaration 
		  //if($trans == true){
		    $insert = 'INSERT INTO Questions VALUES(NULL, :question, :points, :returnType, :functionName)';
		    $db = $dbHandle->prepare($insert);
		    $db->bindParam(':question', $arr['description']);
		    $db->bindParam(':points', $arr['points']);
		    $db->bindParam(':returnType', $arr['return']);
		    $db->bindParam(':functionName', $arr['functionName']);
		    $exec = $db->execute();
		    /*if($exec == false){ //insert did not work
		      $status = 'Questions failed';
		      $trans->rollBack();
		      $this->fixAutoIncrements($currPID+1, $currQID+1); //make sure auto_increment value is one more than number of rows
		      return $status;
		    }*/
                    
               
		    if(!empty($arr['parameters'])){
                      $insertParams = 'INSERT INTO Parameters VALUES(NULL, :dataType, :varName, :question)';
		      $param = $this->dbHandle->prepare($insertParams);
		      foreach((array)$arr['parameters'] as $value){
		        $paramData = explode(' ', $value);
		        $param->bindValue(':dataType', $paramData[0]);
		        $param->bindValue(':varName', $paramData[1]);
		        $param->bindValue(':question', $currQID+1);
		        $exec = $param->execute();
		        /*if($exec == false){
		          $trans->rollBack();
			  $status = 'Params failed';
			  $this->fixAutoIncrements($currPID+1, $currQID+1); //make sure auto_increment value is one more than number of rows
		          return $status;
		        }*/
		      }
		    }
		  
		    $insertConstraints = 'INSERT INTO Constraints VALUES(:question, :const)';
		    $cnstrt = $this->dbHandle->prepare($insertConstraints);
		    foreach((array)$arr['constraints'] as $value){
		      if($value != null){
		        $cnstrt->bindValue(':question', $currQID+1);
		        $cnstrt->bindValue(':const', $value);
		        $exec = $cnstrt->execute();
		        /*if($exec == false){
		          $trans->rollBack();
			  $status = 'Constraints failed';
			  $this->fixAutoIncrements($currPID+1, $currQID+1); //make sure auto_increment value is one more than number of rows
		          return $status;
		        }*/
		      }
		    }
		    $status = 'Success';
	            //echo $status;
		    //$com = $trans->commit();
		    $dbHandle->query('COMMIT');
		    //$this->fixAutoIncrements($currPID+1, $currQID+1); //make sure auto_increment value is one more than number of rows
		    return $status;
		  /*}
		  else{
		    $status = 'Transaction error';
		    echo $status;
		    return $status;
		  }*/
		}
		catch(PDOException $excp){
		  //$dbHandle->query('ROLLBACK');
		  echo 'Database insertion error: ' . $excp->getMessage();
		  $trans->rollBack();
		  $this->fixAutoIncrements($currPID+1, $currQID+1); //make sure auto_increment value is one more than number of rows
		  return 'Failure';
		}
    }
    public function editQuestion(){
    
    }
  }
  class exam extends model{
    private $examQuestions;
    private $questionBank; //questions stored in database
    private $examName;

    public function getQuestionBank(){
      if(!empty($this->questionBank)){
        print_r(json_encode($self->questionBank));
      }
      else{
        $questions = 'SELECT Questions.returnType, Questions.functionName, Questions.Text, Questions.Points, ';
	$questions .= 'GROUP_CONCAT(Parameters.dataType SEPARATOR \', \') AS \'parameter types\', ';
	$questions .= 'GROUP_CONCAT(DISTINCT Parameters.varName SEPARATOR \', \') AS \'parameter names\', ';
	$questions .= 'GROUP_CONCAT(DISTINCT Constraints.Constraint SEPARATOR \', \') AS \'Constraint\' ';
	$questions .= 'FROM Questions LEFT JOIN Parameters ON Questions.QuestionID = Parameters.QuestionID ';
	$questions .= 'LEFT JOIN Constraints ON Questions.QuestionID = Constraints.QuestionID GROUP BY Questions.QuestionID';
	$exec = $this->dbHandle->prepare($questions);
	$exec->execute();
	$this->questionBank = $exec->fetchAll(PDO::FETCH_ASSOC);
	$QBank = json_encode($this->questionBank);
	print_r($QBank);
      }
    }
    public function __construct(){
      $this->dbHandle = $this->connectToDB();
      $examQuestions = array();
      //$questionBank = array();
    }
    public function getExamID($examName){
      $stmt = $this->dbHandle->prepare('SELECT ExamID FROM Exam WHERE ExamName = :name');
      $stmt->bindParam(':name', $examName);
      $stmt->execute();
      $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res[0]['ExamName'];
    }
    public function newExam($examName, $questionArray){
      try{
        $query = 'INSERT INTO Exam VALUES(NULL, :name, eb86, DEFAULT, DEFAULT, DEFAULT)'; //using myself as temporary admin
        $stmt = $this->dbHandle->prepare($query);
        $stmt->bindParam(':name', $examName);
        $stmt->execute();
        foreach($questionArray as $value){
	  $this->addQuestion($this->getExamID($examName), $value);
	}
	return 'Success';
      }
      catch(PDOException $excp){
        return 'Exam addition failure: ' . $excp->getMessage();
      }
    }
    public function addQuestion($examID, $question){
      try{
        $quest = 'SELECT QuestionID FROM Questions WHERE `Text` = :question LIMIT 1'; /* Probably need to get parameters and constraints as well */
	$stmt1 = $this->dbHandle->prepare($quest);
	$stmt1->bindParam(':question', $question);
	$stmt1->execute();
	$res = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $query = 'INSERT INTO ExamQuestions VALUES(:examID, :question)';
        $stmt = $this->dbHandle->prepare($query);
        $stmt->bindParam(':question', $res[0]['QuestionID']);
	$stmt->bindParam(':examID', $examID);
        if($stmt->execute() == true){
          return 'Success';
        }
        else{
	  return 'Failure';
	}
      }
      catch(PDOException $excp){
        return 'Insertion error: ' . $excp->getMessage();
      }
    }
    public function displayExam($examName){
      $id = $this->getExamID($examName);

    }
    public function removeQuestion($question){
    
    }
  }
  function main(){
    model::handle_curl_Request();
  }
  main();
