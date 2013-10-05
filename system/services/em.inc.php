<?php

class EventManager implements IInjectable {

   private $subscribers = array();
   private $services = array();
   

   //--------------------------------------------------------------
   public function inject ($serviceName, $service) {
      
	  $this->services[$serviceName] = $service;
   }
   
   //--------------------------------------------------------------
   //--------------------------------------------------------------   
   public function addEventListener($event, $obj, $proc) {
      	    
	  if (!isset($this->subscribers[$event]))
	     $this->subscribers[$event] = array();
	  
	  $subscriber = array();
	  $subscriber['object'] = $obj;
	  $subscriber['procedure'] = $proc;
	  
	  $this->subscribers[$event][] = $subscriber;
   }
   
   //--------------------------------------------------------------
   //--------------------------------------------------------------  
   public function dispatchEvent($event) {
   
      $sm = $this->services['ServiceManager'];
	  
	  $sm->ss('user_event_dispatch', 'h>dispatching user event: '.$event,
	   'z>subscribers: ',
	   isset($this->subscribers[$event]) ? count($this->subscribers[$event]) : false) ;
	  
      $args = func_get_args();
	  array_shift($args);
	  
	  $s = '';
	  if (isset($this->subscribers[$event])) {
	  
	     $subscribers = $this->subscribers[$event];
	 
		 foreach ($subscribers as $subscriber) {
		    
			$s .= call_user_func_array(array($subscriber['object'], $subscriber['procedure']), $args);
		 }
	  }
	  
	  return $s;
	  
   }
   
   
}








?>