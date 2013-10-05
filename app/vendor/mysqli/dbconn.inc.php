<?php


class mysqli_dbconn {

   private $creds;
   private $_db=null;

   //----------------------------------
   public function connect() {
   
      $this->_db = @new mysqli (
	     $this->creds['host'], $this->creds['username'], 
		 $this->creds['password'], $this->creds['database'] );
         
       if ($this->_db->connect_errno) {        
             throw new Exception('connection error '.$this->_db->connect_error);
          }
   }
  
   //----------------------------------
   public function setConnParams($creds) {
   
      $this->creds = $creds;
   }
   
   //----------------------------------
   public function get() {
      if (is_null($this->_db)) {
             $this->connect();
          }
         
          return $this->_db;
   }
}




?>