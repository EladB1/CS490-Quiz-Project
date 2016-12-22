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
      if(empty($request)){
        $message = '400: HTTP Request not recognized';
	print_r(json_encode((array)$message));
	return;
      }
      switch($request['RequestType']){
        case 'login':
	  $user = new user();
	  $login = $user->login((array)$request['Data']);
	  print_r($login);
	  break;
        case 'getQuestionBank':
	  $ex = new exam();
	  $ex->getQuestionBank();
	  break;
	case 'filterQuestionBank':
	  $ex = new exam();
	  $arr = (array)$request['Data']; 
	  $ex->getQuestionBank((string)$arr['filter']);
	  break;
	case 'addExam':
	  $ex = new exam();
	  $arr = (array)$request['Data'];
          $insert = $ex->newExam((string)$arr['ExamName'], (array)$arr['Questions']); //questions will be added in here
	  print_r(json_encode((array)$insert));
	  break;
	case 'addToQuestionBank':
	  $question = new question();
	  $insert = $question->addToQuestionBank((array)$request['Data']);
	  print_r(json_encode((array)$insert));
	  break;
	case 'adminExamList':
	  $admin = new user();
	  $query = 'SELECT ExamName, ExamStatus, GradingStatus, scores FROM Exam';
          $list =(array)$admin->getExamList($query);
	  print_r(json_encode($list));
	  break;
	case 'studentExamList':
	  $query = 'SELECT ExamName FROM Exam WHERE examStatus = \'Active\'';
	  $stdnt = new student();
	  $list = (array)$stdnt->getExamList($query);
	  print_r(json_encode($list));
	  break;
	case 'activateExam':
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
	  if($attempted != 0)
	    $res = 'Not allowed';
	  else
	    $res = 'Allowed';
	  print_r(json_encode((array)$res));
	  break;
	case 'submitAnswer':
	  $ex = new exam();
	  $arr = (array)$request['Data'];
	  $tmp = (array)$arr[0];
	  $examID = $ex->getExamID((string)$tmp['examName']);	
	  foreach($arr as $array){
	    $res = $ex->storeAnswer((array)$array);
	    print_r(json_encode((array)$res));
		echo '</br>';
	  }
	  if($res == 'Success'){
	    $res2 = $ex->saveGrade($examID, (string)$tmp['userID']);
            print_r(json_encode((array)$res2));
			echo '</br>';
	  }
	  else
	    print_r(json_encode((array)'Cannot save score'));
	  break;
	case 'seeExam':
          $ex = new exam();
	  $arr = (array)$request['Data'];
	  $examQuestions = $ex->displayExam((string)$arr['ExamName']);
	  print_r(json_encode($examQuestions));
	  break;
	case 'releaseGrades':
	  $ex = new exam();
	  $arr = (array)$request['Data'];
	  $rel = $ex->releaseGrades((string)$arr['ExamName']);
	  print_r(json_encode((array)$rel));
	  break;
	case 'seeGrade':
	  $st = new student();
	  $arr = (array)$request['Data'];
	  $res = $st->seeScore((string)$arr[0]);
	  print_r(json_encode((array)$res));
	  break;
	case 'seeFeedback':
	  $st = new student();
	  $arr = (array)$request['Data'];
	  $feedback = $st->reviewTest((string)$arr['userID'], (string)$arr['ExamName']);
	  print_r(json_encode((array)$feedback));
	  break;
	default:
	  $message = '400: HTTP Request not recognized';
	  print_r(json_encode((array)$message));
	  break;
      }
    }
  }
  class user extends model{
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
      return $user;
    }
    public function getExamList($str){ //pass in string to differentiate between student exam list and admin exam lists
      $dbHandle = $this->connectToDB();
      $stmt = $dbHandle->prepare($str);
      $stmt->execute();
      $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $exams;
    }
  }
  class student extends user{
    public function reviewTest($stdnt, $examName){
      try{
        $dbHandle = $this->connectToDB();
	$query = 'SELECT functionName, `Text`, Points, Questions.returnType, params.dataTypes AS \'Parameter types\', params.varNames AS \'Parameter names\', ';
	$query .= 'consts.Constraints, Answer.Code, Answer.FeedBack, Answer.pointsReceived, Answer.returnType AS \'Correct returnType\', tc.Inputs AS \'Inputs\', ';
	$query .= 'tc.Outputs AS \'ExpectedOutputs\' FROM Questions JOIN Answer ON Questions.QuestionID = Answer.QuestionID LEFT JOIN(SELECT QuestionID, ';
	$query .= 'GROUP_CONCAT(DISTINCT varName) AS \'varNames\', GROUP_CONCAT(dataType) AS \'dataTypes\' FROM Parameters GROUP BY QuestionID)params ON ';
	$query .= 'Questions.QuestionID = params.QuestionID LEFT JOIN(SELECT QuestionID, GROUP_CONCAT(DISTINCT `Constraint`) AS \'Constraints\' FROM Constraints GROUP BY ';
	$query .= 'QuestionID) consts ON Questions.QuestionID = consts.QuestionID JOIN ExamQuestions ON ExamQuestions.QuestionID = Questions.QuestionID JOIN Exam ON ';
	$query .= 'Exam.ExamID = ExamQuestions.ExamID LEFT JOIN (SELECT QuestionID, GROUP_CONCAT(Input SEPARATOR \'; \') AS \'Inputs\', GROUP_CONCAT(ExpectedOutput ';
	$query .= 'SEPARATOR \'; \') AS \'Outputs\' FROM TestCases GROUP BY QuestionID)tc ON Questions.QuestionID = tc.QuestionID WHERE Exam.ExamName = :examName AND ';
	$query .= 'UserID = :stdnt AND Exam.ExamID = Answer.ExamID GROUP BY Questions.QuestionID';
	$stmt = $dbHandle->prepare($query);
        $stmt->bindParam(':examName', $examName);
        $stmt->bindParam(':stdnt', $stdnt);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($res))
          return 'Cannot view exam details';
        return $res;
      }
      catch(PDOException $excp){
        return $excp->getMessage();
      }
    }
    public function seeScore($stdnt){
      try{
        $dbHandle = $this->connectToDB();
        $stmt = $dbHandle->prepare('SELECT Exam.ExamName, Points_Scored, TotalPoints FROM Scores JOIN Exam ON Scores.ExamID = Exam.ExamID WHERE StudentID = :stdnt AND Exam.scores = \'Released\'');
        $stmt->bindParam(':stdnt', $stdnt);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($res))
          return 'Score not available for viewing.';
        return $res;
      }
      catch(PDOException $excp){
      	return $excp->getMessage();
      }
    }
  }
  class question extends model{
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
	$query = 'SELECT COUNT(*) FROM '. $tableName; //doesn't allow you to bindParam with table name
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
      $currPID = $this->getMaxID('Parameters', 'paramID'); //auto_increment not reset after rollback, could break logic
      $currQID = $this->getMaxID('Questions', 'QuestionID');
      try{
        $dbHandle = $this->dbHandle;
	$trans = $dbHandle->beginTransaction();
        $insert = 'INSERT INTO Questions VALUES(NULL, :question, :points, :returnType, :functionName, :diff)';
        $db = $dbHandle->prepare($insert);
        $db->bindParam(':question', $arr['description']);
        $db->bindParam(':points', $arr['points']);
        $db->bindParam(':returnType', $arr['return']);
        $db->bindParam(':functionName', $arr['functionName']);
        $db->bindParam(':diff', $arr['Difficulty']);
	$exec = $db->execute();          
	
	if(!empty($arr['parameters'])){
          $insertParams = 'INSERT INTO Parameters VALUES(NULL, :dataType, :varName, :question)';
	  $param = $this->dbHandle->prepare($insertParams);
	  foreach((array)$arr['parameters'] as $value){
	    $paramData = explode(' ', $value);
	    $param->bindValue(':dataType', $paramData[0]);
	    $param->bindValue(':varName', $paramData[1]);
	    $param->bindValue(':question', $currQID+1);
	    $exec = $param->execute();
	  }
	}
		  
        $insertConstraints = 'INSERT INTO Constraints VALUES(:question, :const)';
	$cnstrt = $this->dbHandle->prepare($insertConstraints);
	foreach((array)$arr['constraints'] as $value){
	  if($value != null){
	    $cnstrt->bindValue(':question', $currQID+1);
	    $cnstrt->bindValue(':const', $value);
	    $exec = $cnstrt->execute();
	  }
	}

        $tests = (array)$arr['TestCases'];
        if($tests[0] != NULL and $tests[1] != NULL){
          $insertTestCases = 'INSERT INTO TestCases VALUES(NULL, :question, :input, :expectedOutput)';
	  $testCases = $this->dbHandle->prepare($insertTestCases);
	  $len = count((array)$tests[0]);
	  for($i = 0; $i <= $len; $i+=1){
	    $testCases->bindValue(':question', $currQID+1);
	    $testCases->bindValue(':input', $tests[0][$i]);
	    $testCases->bindValue('expectedOutput', $tests[1][$i]);
	    $exec = $testCases->execute();	  
	  }
        }
	$status = 'Success';
	$dbHandle->query('COMMIT');
	return $status;
      }
      catch(PDOException $excp){
        echo 'Database insertion error: ' . $excp->getMessage();
	$trans->rollBack();
	$this->fixAutoIncrements($currPID+1, $currQID+1); //make sure auto_increment value is one more than number of rows
	return 'Failure';
      }
    }
  }
  class exam extends model{
    private $questionBank; //questions stored in database
    public function __construct(){
      $this->dbHandle = $this->connectToDB();
    }
    public function getQuestionBank($filter = ''){
      if(!empty($this->questionBank) and $filter == '')
        print_r(json_encode($self->questionBank));
      else{
        $questions = 'SELECT Questions.returnType, Questions.functionName, Questions.Text, Questions.Points, Questions.Difficulty, params.paramTypes AS \'parameter types\', ';
	$questions .= 'params.paramNames AS \'parameter names\', constr AS \'Constraint\', GROUP_CONCAT(tc.Input SEPARATOR \', \') AS \'Inputs\', ';
	$questions .= 'GROUP_CONCAT(tc.ExpectedOutput SEPARATOR \', \') AS \'Expected Outputs\' FROM Questions LEFT JOIN (SELECT QuestionID, ';
	$questions .= 'GROUP_CONCAT(Parameters.dataType SEPARATOR \', \') AS \'paramTypes\', GROUP_CONCAT(DISTINCT Parameters.varName SEPARATOR \', \') AS \'paramNames\' ';
	$questions .= 'FROM Parameters GROUP BY Parameters.QuestionID)params ON Questions.QuestionID = params.QuestionID LEFT JOIN (SELECT QuestionID, GROUP_CONCAT(DISTINCT ';
	$questions .= '`Constraint` SEPARATOR \', \') AS \'constr\' FROM Constraints GROUP BY QuestionID)cnst ON Questions.QuestionID = cnst.QuestionID LEFT JOIN ';
	$questions .= '(SELECT QuestionID, GROUP_CONCAT(Input SEPARATOR \'; \') AS \'Input\', GROUP_CONCAT(ExpectedOutput SEPARATOR \'; \') AS \'ExpectedOutput\' ';
	$questions .= 'FROM TestCases GROUP BY QuestionID)tc ON tc.QuestionID = Questions.QuestionID GROUP BY Questions.QuestionID';
	if($filter != '')
	  $questions .= ' ORDER BY '. $filter;
	$exec = $this->dbHandle->prepare($questions);
	$exec->execute();
	$this->questionBank = $exec->fetchAll(PDO::FETCH_ASSOC);
	$QBank = json_encode($this->questionBank);
	print_r($QBank);
      }
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
        $quest = 'SELECT QuestionID FROM Questions WHERE `functionName` = :question LIMIT 1'; /* Probably need to get parameters and constraints as well */
	$stmt1 = $this->dbHandle->prepare($quest);
	$stmt1->bindParam(':question', $question);
	$stmt1->execute();
	$res = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $query = 'INSERT INTO ExamQuestions VALUES(:examID, :question)';
        $stmt = $this->dbHandle->prepare($query);
        $stmt->bindValue(':question', $res[0]['QuestionID']);
	$stmt->bindValue(':examID', $examID);
        if($stmt->execute() == true)
          return 'Success';
        else
	  return 'Failure';	
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
      if($value == 'Released')
        return true;
      else if($value == 'Not Released')
        return false;
    }
    public function displayExam($examName){
      $questions = 'SELECT functionName, `Text`, Points, returnType, params.dataTypes AS \'Parameter types\', params.varNames AS \'Parameter names\', ';
      $questions .= 'consts.constraints AS \'Constraints\', Questions.Difficulty, tc.inputs, tc.outputs FROM Questions JOIN ExamQuestions ON ';
      $questions .= 'ExamQuestions.QuestionID = Questions.QuestionID JOIN Exam ON Exam.ExamID = ExamQuestions.ExamID LEFT JOIN (SELECT QuestionID, '; 
      $questions .= 'GROUP_CONCAT(dataType) AS \'dataTypes\', GROUP_CONCAT(DISTINCT varName) AS \'varNames\' FROM Parameters GROUP BY QuestionID)params ON ';
      $questions .= 'params.QuestionID = Questions.QuestionID LEFT JOIN (SELECT QuestionID, GROUP_CONCAT(DISTINCT Constraints.Constraint) AS \'constraints\' FROM Constraints ';
      $questions .= 'GROUP BY QuestionID)consts ON consts.QuestionID = Questions.QuestionID LEFT JOIN (SELECT QuestionID, GROUP_CONCAT(Input SEPARATOR \'; \') AS \'inputs\', ';
      $questions .= 'GROUP_CONCAT(ExpectedOutput SEPARATOR \'; \') AS \'outputs\' FROM TestCases GROUP BY QuestionID)tc ON tc.QuestionID = Questions.QuestionID WHERE ';
      $questions .= 'Exam.ExamName = :examName GROUP BY Questions.QuestionID;';
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
      else
        return 'Exam: ' . $examName . ' already released.';
    }
    public function getPointsScored($student, $exam){
      try{      
        $stmt = $this->dbHandle->prepare('SELECT SUM(pointsReceived) AS \'total\' FROM Answer WHERE UserID = :stdnt AND ExamID = :exam GROUP BY ExamID, UserID');
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
        $query = 'SELECT SUM(Questions.Points) AS \'Points\' FROM Questions JOIN ExamQuestions ON Questions.QuestionID = ExamQuestions.QuestionID WHERE ExamID = :exam';
	$stmt = $this->dbHandle->prepare($query);
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
	$query = 'SELECT 1 FROM Answer WHERE ExamID = :examID AND userID = :student LIMIT 1';
	$stmt = $this->dbHandle->prepare($query);
	$stmt->bindParam(':examID', $exam);
	$stmt->bindParam(':student', $student);
	$stmt->execute();
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
	    $questionID = $this->dbHandle->prepare('SELECT Questions.QuestionID FROM ExamQuestions JOIN Questions ON ExamQuestions.QuestionID = Questions.QuestionID WHERE Questions.Text = :qText AND ExamID = :examID');
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
