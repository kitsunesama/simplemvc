<?php

class SessionManager {
   
   public function persist_get(& $get_vars) {
      
	  if (!session_id())
	     return false;
		 
		 
	  if (!isset($_SESSION['persist_get_vars']))
	     $_SESSION['persist_get_vars'] = array();
		 
	  foreach ($get_vars as $key=>$val) {
	     $_SESSION['persist_get_vars'][$key] = $val;
      }
	  
	  $saved_get_vars = array_diff_key($_SESSION['persist_get_vars'], $get_vars);
	  
	  foreach ($saved_get_vars as $key=>$val) {
	  
	     $get_vars[$key] = $val;
	  }	  
   }
}










?>