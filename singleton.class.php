<?php
  class singleton{ //A database handler class that can only be instantied once
    private $db_Connection;
    private function  __construct(){
     $dbPass = 'xxxxxxxx'; //this is a junk password for security purposes; the actual password is on afs.
     try{
       return $this->db_Connection = new PDO('mysql:host=sql2.njit.edu; database=eb86','eb86', $dbPass);
     }
     catch(PDOException $exceptionHandler){
       return 'Database connection error: ' . $exceptionHandler;
     }
    }
    public static function connectToDB(){
      if(isset($db_Connection)){
      	return $this->db_Connection;
      }
      else{
      	new singleton();
      }
    }
    
  }
?>
