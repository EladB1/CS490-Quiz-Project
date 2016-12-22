<?php
//test script for back end //not used in actual project
  function sendCurl($requesttype, $data){
    $ch = curl_init();
    $inf = array('RequestType' => $requesttype, 'Data' => $data);
    echo '<br></br>request: ';
    print_r($inf);
    echo '<br></br>' . 'response: ';
    $info = json_encode($inf);
    curl_setopt($ch, CURLOPT_URL, 'http://afsaccess3.njit.edu/~eb86/cs490/model.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $info);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);

    curl_close($ch);
    return $resp;
  }
  echo '<b><center><h1>CS490 Backend Test</h1></center></b>';
  $arr = array('username' => 'eb86', 'password' => 'l33t-h@ck$');
  $resp = sendCurl('login', $arr);
  echo '<pre>';
  print_r($resp);
  echo '</pre>';
  echo '</br>';
  print_r(sendCurl('getQuestionBank', ' '));
  echo '</br>';
  print_r(sendCurl('filterQuestionBank', array('filter' => 'Points')));
  echo '</br>';
  print_r(sendCurl('hi', ' ')); //should not be recognized
  echo '</br>';
  $param = array('int i', 'int n');
  $constraints = array('for loop', null, null, null);
  $cases = array(array('1,2', '2,5', '3,0'), array('1', '32', '1'));
  $question = array('description' => 'Return i to the nth power.', 'points' => 5, 'return' => 'int','functionName' => 'iToThe-nth', 'Difficulty' => 'Easy', 'parameters' => $param, 'constraints' => $constraints, 'TestCases' => $cases);
  
  //print_r(sendCurl('addToQuestionBank', $question));
  echo '</br>';
  print_r(sendCurl('adminExamList', ' '));

  echo '</br>';
  $exam = array('ExamName' => 'sample-exam2', 'Questions' => array('printHelloWorld', 'printHello', 'nothing', 'nthPowerOfPi'));
  //print_r(sendCurl('addExam', $exam));

  echo '</br>';
  //print_r(sendCurl('activateExam', array('ExamName' => 'sample-exam2')));
  
  echo '</br>';
  //print_r(sendCurl('releaseGrades', array('ExamName' => 'sampleExam')));
  
  echo '</br>';
  print_r(sendCurl('seeExam', array('ExamName' => 'realTest')));

   echo '</br>';
   //INSERT INTO Answer VALUES(NULL, :code, :feedback, :pointsRcv ,:user, :question, :exam, :returnType, :returnVal)
   $code = 'def hi():' . "\n" . '  return 1.1 * 3';
   //print_r(sendCurl('submitAnswer', array(array('code' => '', 'feedback' => 'No answer submitted', 'credit' => 0, 'userID' => 'xz312','questionText' => 'Write a function that returns an arithmetic expression based on the value of op.', 'examName' =>'calcTest','returnType' => 'float', 'returnValue' => ''))));
   print_r(sendCurl('submitAnswer', array(array('code' => '', 'feedback' => 'You did not enter anything', 'credit' => 0, 'userID' => 'xz312','questionText' => 'Write a function that uses the op parameter and returns a op b. For example, if op parameter is *, return a * b op can be either +,-,*,/', 'examName' =>
   'finalExam','returnType' => 'float', 'returnValue' => ''),array('code' => '', 'feedback' => 'You did not enter anything', 'credit' => 0, 'userID' => 'wx411','questionText' => 'Return parameter a and b', 'examName' => 'finalExam','returnType' => 'int', 'returnValue' => ' '))));
   
   echo '</br>';
   print_r(sendCurl('seeGrade', 'wx411'));

   echo '</br>';
   print_r(sendCurl('seeFeedback', array('userID' => 'wx411', 'ExamName' => 'funTest')));
   
   echo '<br>';
   print_r(sendCurl('checkAttempts', array('userID' => 'wx411', 'examName' => 'hi')));
?>
