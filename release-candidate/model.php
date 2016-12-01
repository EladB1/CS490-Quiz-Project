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
	case 'filterQuestionBank':
	  //do something
	  $ex = new exam();
	  $arr = (array)$request['Data'];
	  //var_dump($arr['filter']);
	  //switch($arr['filter']){} 
	  $ex->getQuestionBank((string)$arr['filter']);
	  break;
	case 'addExam':
	  //do something
	  $ex = new exam();
	  $arr = (array)$request['Data'];
          $insert = $ex->newExam((string)$arr['ExamName'], (array)$arr['Questions']); //questions will be added in here
	  print_r(json_encode((array)$insert));
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
	  $admin = new admin();
	  $query = 'SELECT ExamName, ExamStatus, GradingStatus, scores FROM Exam';
          $list =(array)$admin->getExamList($query);
	  print_r(json_encode($list));
	  break;
	case 'studentExamList':
	  //do something
	  $query = 'SELECT ExamName FROM Exam WHERE examStatus = \'Active\'';
	  $stdnt = new student();
	  $list = (array)$stdnt->getExamList($query);
	  print_r(json_encode($list));
	  break;
	case 'activateExam':
	  //do something
	  $ex = new exam();
	  $arr = (array)$request['Data'];
	  $act = $ex->activate((string)$arr['ExamName']);
	  print_r(json_encode((array)$act));
	  break;
	case 'checkAttempts':
	  $ex = new exam();
	  $arr = (array)$request['Data'];
	  $examID = $ex->getExamID((string)$arr['examName']);
	  $attempted = $ex->checkAttempts((string)$arr['userID'], $examID);
	  if($attempted != 0){
	    $res = 'Not allowed';
	  }
	  else{
		$res = 'Allowed';
	  }
	  print_r(json_encode((array)$res));
	  break;
	case 'submitAnswer':
	  //do something
	  $ex = new exam();
	  $arr = (array)$request['Data'];
	  $tmp = (array)$arr[0];
	  //var_dump($tmp);
	  $examID = $ex->getExamID((string)$tmp['examName']);	  
	  foreach($arr as $array){
	    $res = $ex->storeAnswer((array)$array);
	    //var_dump($array);
	    print_r(json_encode((array)$res));
	  }
	  if($res == 'Success'){
	    $res2 = $ex->saveGrade($examID, (string)$tmp['userID']);
	    //var_dump($res2);
        print_r(json_encode((array)$res2));
	  }
	  else{
	    print_r(json_encode((array)'Cannot save score'));
	  }
	  break;
	case 'seeExam':
	  //do something
          $ex = new exam();
	  $arr = (array)$request['Data'];
	  $examQuestions = $ex->displayExam((string)$arr['ExamName']);
	  print_r(json_encode($examQuestions));
	  break;
	case 'releaseGrades':
	  //do something
	  $ex = new exam();
	  $arr = (array)$request['Data'];
	  $rel = $ex->releaseGrades((string)$arr['ExamName']);
	  //var_dump($arr);
	  print_r(json_encode((array)$rel));
	  break;
	case 'seeGrade':
	  //do something
	  $st = new student();
    $arr = (array)$request['Data'];
	  $res = $st->seeScore((string)$arr[0]);
	  print_r(json_encode((array)$res));
	  break;
	case 'seeFeedback':
	  //do something
	  $st = new student();
	  $arr = (array)$request['Data'];
	  $feedback = $st->reviewTest((string)$arr['userID'], (string)$arr['ExamName']);
	  print_r(json_encode((array)$feedback));
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
    /*public function __construct($username){
      $this->username = username;
    }*/
    public function getExamList($str){ //pass in a string to differentiate between the student exam list and admin exam lists; as of now neither are user dependent, but that may change
      $dbHandle = $this->connectToDB();
      $stmt = $dbHandle->prepare($str);
      $stmt->execute();
      $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $exams;
    }
  }
  class student extends userBase{
    public function reviewTest($stdnt, $examName){
      try{
        $dbHandle = $this->connectToDB();
        /*$query = 'SELECT functionName, `Text`, Points, Questions.returnType, GROUP_CONCAT(Parameters.dataType) AS \'Parameter types\', GROUP_CONCAT(Parameters.varName) AS
      \'Parameter names\', GROUP_CONCAT(Constraints.Constraint) AS \'Constraints\', Answer.Code, Answer.FeedBack, Answer.pointsReceived, Answer.returnType AS \'Correct
      returnType\', Answer.returnValue AS \'Correct result\' FROM Questions JOIN ExamQuestions ON ExamQuestions.QuestionID = Questions.QuestionID JOIN Exam ON Exam.ExamID =
      ExamQuestions.ExamID JOIN Answer ON Answer.ExamID = Exam.ExamID LEFT JOIN Parameters ON Parameters.QuestionID = Questions.QuestionID LEFT JOIN Constraints ON
      Constraints.QuestionID = Questions.QuestionID WHERE Exam.ExamName = :examName AND UserID = :stdnt AND Exam.scores = \'Released\' GROUP BY ExamQuestions.QuestionID';
        */


	$query = 'SELECT functionName, `Text`, Points, Questions.returnType, `Parameter types`,`Parameter names`, `Constraints`, Answer.Code, Answer.FeedBack,
	Answer.pointsReceived, Answer.returnType AS \'Correct returnType\', Answer.returnValue AS \'Correct result\' FROM Questions JOIN Answer ON Questions.QuestionID =
	Answer.QuestionID JOIN cs490_QuestionInfo ON Questions.QuestionID = cs490_QuestionInfo.QuestionID JOIN ExamQuestions ON ExamQuestions.QuestionID = Questions.QuestionID
	JOIN Exam ON Exam.ExamID = ExamQuestions.ExamID WHERE Exam.ExamName = :examName AND UserID = :stdnt GROUP BY Questions.QuestionID';
	
	$stmt = $dbHandle->prepare($query);
        $stmt->bindParam(':examName', $examName);
        $stmt->bindParam(':stdnt', $stdnt);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($res)){
          return 'Cannot view exam details';
        }
        return $res;
      }
      catch(PDOException $excp){
        return $excp->getMessage();
      }
    }
    public function seeScore($stdnt){
      try{
        //var_dump($stdnt);
        $dbHandle = $this->connectToDB();
        $stmt = $dbHandle->prepare('SELECT Exam.ExamName, Points_Scored, TotalPoints FROM Scores JOIN Exam ON Scores.ExamID = Exam.ExamID WHERE StudentID = :stdnt AND Exam.scores = \'Released\'');
        $stmt->bindParam(':stdnt', $stdnt);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($res)){
          return 'Score not available for viewing.';
        }
        return $res;
      }
      catch(PDOException $excp){
      	return $excp->getMessage();
      }
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
		    $insert = 'INSERT INTO Questions VALUES(NULL, :question, :points, :returnType, :functionName, :diff)';
		    $db = $dbHandle->prepare($insert);
		    $db->bindParam(':question', $arr['description']);
		    $db->bindParam(':points', $arr['points']);
		    $db->bindParam(':returnType', $arr['return']);
		    $db->bindParam(':functionName', $arr['functionName']);
		    $db->bindParam(':diff', $arr['Difficulty']);
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
    public function getQuestionBank($filter = ''){
      if(!empty($this->questionBank) and $filter == ''){
        print_r(json_encode($self->questionBank));
      }
      else{
      	//var_dump($filter);
        $questions = 'SELECT Questions.returnType, Questions.functionName, Questions.Text, Questions.Points, Questions.Difficulty, ';
	    $questions .= 'GROUP_CONCAT(Parameters.dataType SEPARATOR \', \') AS \'parameter types\', ';
	    $questions .= 'GROUP_CONCAT(DISTINCT Parameters.varName SEPARATOR \', \') AS \'parameter names\', ';
	    $questions .= 'GROUP_CONCAT(DISTINCT Constraints.Constraint SEPARATOR \', \') AS \'Constraint\' ';
	    $questions .= 'FROM Questions LEFT JOIN Parameters ON Questions.QuestionID = Parameters.QuestionID ';
	    $questions .= 'LEFT JOIN Constraints ON Questions.QuestionID = Constraints.QuestionID GROUP BY
	    Questions.QuestionID';
		if($filter != '')
			$questions .= ' ORDER BY '. $filter;
	    $exec = $this->dbHandle->prepare($questions);
	    //if($filter != null)
	      //$exec->bindParam(':filter', $filter);
	      //var_dump($exec);
	    //else
	      //$exec->bindParam(':filter', ' ');
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
      $dbHandle = $this->connectToDB();
      $stmt = $dbHandle->prepare('SELECT ExamID FROM Exam WHERE ExamName = :name');
      $stmt->bindValue(':name', $examName);
      $stmt->execute();
      $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res[0]['ExamID'];
    }
    public function getAutoIncrement(){
      $stmt = $this->dbHandle->prepare('SELECT MAX(ExamID) FROM Exam');
      $stmt->execute();
      $max = $stmt->fetchAll();
      return $max[0][0];
    }
    public function newExam($examName, $questionArray){
      try{
        $currID = $this->getAutoIncrement();
        $query = 'INSERT INTO Exam VALUES(NULL, :name, \'eb86\', DEFAULT, DEFAULT, DEFAULT)'; //using myself as temporary admin
        $stmt = $this->dbHandle->prepare($query);
        $stmt->bindParam(':name', $examName);
        $stmt->execute();
	    $id = $this->getExamID($examName);
        foreach($questionArray as $value){
	      if($this->addQuestion($id , $value) != 'Success'){
	        
	       $this->$dbHandle->query('ALTER TABLE Exam AUTO_INCREMENT = ' . $currID+1);
	       return 'Failure';
	      }
	    }
	    return 'Success';
      }
      catch(PDOException $excp){
	$this->$dbHandle->query('ALTER TABLE Exam AUTO_INCREMENT = ' . $currID+1);
        return 'Exam addition failure: ' . $excp->getMessage();
      }
    }
    public function addQuestion($examID, $question){
      try{
        $dbHandle = $this->connectToDB();
        $quest = 'SELECT QuestionID FROM Questions WHERE `functionName` = :question LIMIT 1'; /* Probably need to get parameters and constraints as well */
	    $stmt1 = $dbHandle->prepare($quest);
	    $stmt1->bindParam(':question', $question);
	    $stmt1->execute();
	    $res = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $query = 'INSERT INTO ExamQuestions VALUES(:examID, :question)';
        $stmt = $dbHandle->prepare($query);
        $stmt->bindValue(':question', $res[0]['QuestionID']);
	    $stmt->bindValue(':examID', $examID);
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
    public function isActive($examName){
      $query = $this->dbHandle->prepare('SELECT examStatus FROM Exam WHERE ExamName = :exam');
      $query->bindValue(':exam', $examName);
      $query->execute();
      $res = $query->fetchAll(PDO::FETCH_ASSOC);
      $value = $res[0]['examStatus'];
      if($value == 'Active'){
        return true;
      }
      else if($value == 'Inactive'){
        return false;
      }
    }
    public function gradeReleased($examName){
      $stmt = $this->dbHandle->prepare('SELECT scores FROM Exam WHERE ExamName = :exam');
      $stmt->bindValue(':exam', $examName);
      $stmt->execute();
      $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $value = $res[0]['scores'];
      if($value == 'Released'){
        return true;
      }
      else if($value == 'Not Released'){
        return false;
      }

    }
    public function displayExam($examName){
      //$id = $this->getExamID($examName);
      $questions = 'SELECT functionName, `Text`, Points, returnType, GROUP_CONCAT(Parameters.dataType) AS \'Parameter types\', 
      GROUP_CONCAT(Parameters.varName) AS \'Parameter names\', GROUP_CONCAT(Constraints.Constraint) AS \'Constraints\', Questions.Difficulty 
      FROM Questions JOIN ExamQuestions ON ExamQuestions.QuestionID = Questions.QuestionID JOIN Exam ON Exam.ExamID = ExamQuestions.ExamID 
      LEFT JOIN Parameters ON Parameters.QuestionID = Questions.QuestionID LEFT JOIN Constraints ON Constraints.QuestionID = Questions.QuestionID 
      WHERE Exam.ExamName = :examName GROUP BY ExamQuestions.QuestionID;';
      $stmt = $this->dbHandle->prepare($questions);
      $stmt->bindParam(':examName', $examName);
      $stmt->execute();
      $exam = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $exam;
    }

    public function activate($examName){
      if(!$this->isActive($examName)){
        try{
          $query = $this->dbHandle->prepare('UPDATE Exam SET examStatus = \'Active\' WHERE ExamName = :name');
	      $query->bindParam(':name', $examName);
	      $query->execute();
	      return 'Exam activated.';
	    }
	    catch(PDOException $excp){
	      return $excp->getMessage();
	    }
      }
      else{
        return 'Exam: ' . $examName . ' already released.';
      }
    }
	public function getPointsScored($student, $exam){
		try{
		        //var_dump($this->dbHandle);      
			$stmt = $this->dbHandle->prepare('SELECT SUM(pointsReceived) AS \'total\' FROM Answer WHERE UserID = :stdnt AND ExamID = :exam');
			$stmt->bindParam(':stdnt', $student);
			$stmt->bindParam(':exam', $exam);
			$stmt->execute();
			$points = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $points[0]['total'];
		}
		catch(PDOException $excp){
			return $excp->getMessage();
			
		}
		
	}
	public function getPointsAvail($student, $exam){
		try{
			
			$query = 'SELECT SUM(Questions.Points) AS \'Points\' FROM Questions JOIN ExamQuestions ON Questions.QuestionID = ExamQuestions.QuestionID ';
			$query .= 'WHERE ExamID = :exam';
			$stmt = $this->dbHandle->prepare($query);
			//$stmt->bindParam(':stdnt', $student);
			$stmt->bindParam(':exam', $exam);
			$stmt->execute();
			$points = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $points[0]['Points'];
		}
		catch(PDOException $excp){
			return $excp->getMessage();
			
		}
		
	}
	public function checkAttempts($student, $exam){
	  try{
		$dbHandle = $this->connectToDB();
	    $query = 'SELECT 1 FROM Answer WHERE ExamID = :examID AND userID = :student LIMIT 1';
	    $stmt = $dbHandle->prepare($query);
	    $stmt->bindParam(':examID', $exam);
	    $stmt->bindParam(':student', $student);
	    $stmt->execute();
	    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    return $stmt->rowCount();
	  }
	  catch(PDOException $excp){
	    echo $excp->getMessage();
	    return false;
	  }
	}
	public function saveGrade($exam, $user){
	  try{
	    $points = $this->getPointsScored($user, $exam);
	    $outof = $this->getPointsAvail($user, $exam);
	    //var_dump($points);
	    $stmt2 = $this->dbHandle->prepare('INSERT INTO Scores VALUES(:examID, :user, :totalpoints, :pointsscored)');
	    $stmt2->bindParam(':examID', $exam);
	    $stmt2->bindParam(':user', $user);	
	    $stmt2->bindParam(':totalpoints', $outof);											
	    $stmt2->bindParam(':pointsscored', $points);
	    $stmt2->execute();
	    return 'Success';
          }
	  catch(PDOException $excp){
	    return $excp->getMessage();
	  }
	}
	public function storeAnswer($arr){
		try{
			$examID = $this->getExamID($arr['examName']);		
			$trans = $this->dbHandle->beginTransaction();
			$questionID = $this->dbHandle->prepare('SELECT Questions.QuestionID FROM ExamQuestions JOIN Questions ON ExamQuestions.QuestionID =
			Questions.QuestionID WHERE Questions.Text = :qText AND ExamID = :examID');
			$questionID->bindParam(':qText', $arr['questionText']);
			$questionID->bindParam(':examID', $examID);
			$questionID->execute();
			$question = $questionID->fetch();
			$questionTxt = $question[0];
			$ins = 'INSERT INTO Answer VALUES(NULL, :code, :feedback, :pointsRcv ,:user, :question, :exam, :returnType, :returnVal)';
			$stmt = $this->dbHandle->prepare($ins);
			$stmt->bindParam(':code', $arr['code']);
			$stmt->bindParam(':feedback', $arr['feedback']);
			$stmt->bindParam(':pointsRcv', $arr['credit']);
			$stmt->bindParam(':user', $arr['userID']);
			$stmt->bindParam(':question', $questionTxt); //get questionID by question text
			$stmt->bindParam(':exam', $examID);
			$stmt->bindParam(':returnType', $arr['returnType']);
			$stmt->bindParam(':returnVal', $arr['returnValue']);
			$stmt->execute();
			$this->dbHandle->query('UPDATE Exam SET GradingStatus = \'Graded\' WHERE ExamID = ' . $examID);
			$this->dbHandle->query('COMMIT');
			return 'Success';
		}
		catch(PDOException $excp){
		        $trans->rollBack();
			return $excp->getMessage();
		}
	}
    public function releaseGrades($examName){
	  if(!$this->gradeReleased($examName)){
            try{
	          $query = $this->dbHandle->prepare('UPDATE Exam SET scores = \'Released\' WHERE ExamName = :name');
	          $query->bindParam(':name', $examName);
	          $query->execute();
	          return 'Scores released';
	        }
	        catch(PDOException $excp){
	          return $excp->getMessage();
	        }
      }
      else{
        return 'Exam: ' . $examName . ' already had its grades released.';
      }
    }
  }
  function main(){
    model::handle_curl_Request();
  }
  main();
?>
