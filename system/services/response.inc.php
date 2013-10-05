<?php

class Response {

   private $headers = array();
   private $page;
   
   
   
   public function setHeader($header) {
   
      $this->headers[] = $header;
   }
   
   public function setPage($page) {
      $this->page = $page;
   }
   
   public function output() {
   
      foreach ($this->headers as $header) {
	     header($header);
	  }
	  
	  echo $this->page;
   }
   
}














?>