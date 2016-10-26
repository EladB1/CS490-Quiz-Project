<?php
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
  print_r(sendCurl('hi', ' ')); //should not be recognized
  echo '</br>';
  $param = array('int n');
  $constraints = array('for loop', null, null, null);
  $question = array('description' => 'Return pi to the nth power.', 'points' => null, 'return' => 'float','functionName' => 'nthPowerOfPi', 'parameters' => $param, 'constraints' => $constraints);
  
  //print_r(sendCurl('addToQuestionBank', $question));
  echo '</br>';
  print_r(sendCurl('adminExamList', ' '));
?>
