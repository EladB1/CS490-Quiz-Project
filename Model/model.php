  <?php
  class model{
    protected $dbHandle;
    public function connectToDB(){
      $dbPass = 'xxxxxxxx';
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
    public function authenticate($userName, $password){
      try{
        $PDO_Obj = $this->connectToDB();
        $query = 'SELECT UserID, Password, Role FROM CS490_Users WHERE UserID = :username AND Password = :password LIMIT 1'; //limit 1 will make it stop after getting result
	$exec = $PDO_Obj->prepare($query);
        $exec->bindParam(':username', $userName);
        $exec->bindParam(':password', $password);
        $exec->execute();
        $queryResult = $exec->fetchAll(PDO::FETCH_ASSOC); //get an associative array from the result
        $results['UserID'] = $userName;
	if(empty($queryResult)){ //nothing was returned by the database
	  $results['Role'] = ' ';
	  $results['loginAttempt'] = false; //bad login attempt
	}
	else if(!empty($queryResult)){ 
	  echo $queryResult['Role'] . "\n";
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
	  $array = $message;
	  return $this->authenticate($array['username'], $array['password']);
    }
  }
  /* main */
  $user = new user();
  $arr = $user->login();
  print_r($arr);
  
?>
                                                                                                                                      

                                                                                                                                                              1,5           Top
