<?php
  class model{
    protected $dbHandle;
    public function connectToDB(){
      $dbPass = 'xxxxxxxx'; //my actual database password was left out for security purposes
      try{
        $this->dbHandle = new PDO('mysql:host=sql2.njit.edu; dbname=eb86', 'eb86', $dbPass);
        $dbObject = $this->dbHandle;
        return $dbObject;
      }
      catch(PDOException $excp){
        return 'Database connection problem: ' . $excp->getMessage() ."\n";

      }
    }
     public function sendJSON($encodedMessage){
      $sendCurl = curl_init();
      curl_setopt($sendCurl, CURLOPT_URL, 'https://web.njit.edu/~dyp6/CS490/MidEndLogin.php');
      curl_setopt($sendCurl, CURLOPT_POST, TRUE);
      curl_setopt($sendCurl, CURLOPT_POSTFIELDS, $encodedMessage);
      $http_header = array('Accept: application/json', 'Content-Type: application/json');
      curl_setopt($sendCurl, CURLOPT_HTTPHEADER, $http_header);

      $message = curl_exec($sendCurl);
      curl_close($sendCurl);
    }
    public function receiveJSON(){
	  $recv = curl_init();
	  curl_setopt($recv, CURLOPT_URL, 'https://web.njit.edu/~dyp6/CS490/MidEndLogin.php');
          curl_setopt($recv, CURLOPT_POST, TRUE);
          curl_setopt($recv, CURLOPT_RETURNTRANSFER, TRUE);
	  $message = curl_exec($recv);
          $arr = json_decode($message);
	  curl_close($recv);
	  return $arr;
    }
  }
  class user extends model{
    public function authenticate($userName, $password){
      try{
        $PDO_Obj = $this->connectToDB();
        $query = 'SELECT UserID, Password, Role FROM CS490_Users WHERE UserID = :username AND Password = :password LIMIT 1'; //limit 1 will make it stop after getting result
        $exec = $PDO_Obj->prepare($query);
        $exec->bindParam(':username', $userName);
        $exec->bindParam(':password', $password);
        $exec->execute();
        $queryResult = $exec->fetchAll(PDO::FETCH_ASSOC); //get an associative array from the result
        $results = array();
        $results['UserID'] = $userName;
        $results['Password'] = $password;
        if(empty($queryResult)){ //nothing was returned by the database
          $results['Role'] = ' ';
          $results['loginAttempt'] = false; //bad login attempt
        }
        else{
          $results['Role'] = $queryResults[0]['Role'];
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
	  $message = $this->receiveJSON();
	  $array = $message;
	  return $this->authenticate($array['username'], $array['password']);
    }
  }
?>
                                                                                                                                                              62,2          Bot

                                                                                                                                                              1,5           Top
