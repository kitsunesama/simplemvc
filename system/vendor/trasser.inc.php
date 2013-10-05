<?php

class ScreenTrasser {

   public function write() {

   
      $args = func_get_args();
	  //echo '<pre>'.print_r($args,true).'</pre>';
	  //return;

          $indent = array_shift($args);
	  $event = array_shift($args);
	  echo '<div style="margin-left:'.$indent.'px">';

	  echo date('d.m.Y H:i:s   ').'<b>'.$event.':</b> ';
   
          $s = '';
	  foreach ($args as $arg ) {
	 
		 if (is_scalar($arg)) {
		 
		    if (substr($arg,0,2)=='h>')
			   $arg4 = '<font color="red"><b>'.substr($arg,2).'</b></font>';
			else  
			if (substr($arg,0,2)=='g>')
			   $arg4 = '<font color="green"><b>'.substr($arg,2).'</b></font>';
			else 
			if (substr($arg,0,2)=='a>')
			   $arg4 = substr($arg,2); 
			else   
			if (substr($arg,0,2)=='s>')
			   $arg4 = ''; 
			else  
			if (substr($arg,0,2)=='z>')
			   $arg4 = '<b>'.substr($arg,2).'</b>'; 
            else
               if (substr($arg,0,2)=='e>')
			   $arg4 = '<font color="red">'.substr($arg,2).'</font>'; 
            else
			   $arg4 = $arg;
			   
		    $s .= $arg4.'<br/>';
		 } else {
		    $s .= '<pre>'.print_r($arg,true).'</pre>';
		 }
	 }
		 echo $s;
	  
          echo '</div>';
   }
}







?>