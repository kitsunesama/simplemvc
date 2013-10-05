<?php


interface IInjectable {
   public function inject($serviceName, $service);
}

interface IConfigurable {
   public function config();
}

interface IDecorator {
   public function wrap($obj);
}

//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
class SysEventManager {

   private $subscribers = array();
   private $skip_events = array();
   
   private $indent = 0;
   
   //-------------------------------------------------
   public function plusIndent($plus) {
   
      $this->indent += $plus;
   }
   //-------------------------------------------------
   public function minusIndent($minus) {
   
      $this->indent -= $minus;
   }
   //-------------------------------------------------
   public function setSkipEvent($event) {
      $this->skip_events[$event] = true;
   }
   //-------------------------------------------------
   public function addEventListener($event, $obj, $proc) {     
	  if (!isset($this->subscribers[$event]))
	     $this->subscribers[$event] = array();
	  
	  $subscriber = array();
	  $subscriber['object'] = $obj;
	  $subscriber['procedure'] = $proc;	  
	  $this->subscribers[$event][] = $subscriber;
   }
   //-------------------------------------------------   
   public function dispatchEvent($event) {
   
      if (array_key_exists($event, $this->skip_events))
	     return false;
   
      $args = func_get_args();
	  array_unshift($args, $this->indent );
	  
      $s = '';
	  
	  if (isset($this->subscribers[$event])) {  
	     $subscribers = $this->subscribers[$event];		 
		 
		 foreach ($subscribers as $subscriber) {    
			$s .= call_user_func_array(array($subscriber['object'], $subscriber['procedure']), $args);
		 }
	  }  
	   if (isset($this->subscribers['*'])) {  
	     $subscribers = $this->subscribers['*'];		 
		 
		 foreach ($subscribers as $subscriber) {    
			$s .= call_user_func_array(array($subscriber['object'], $subscriber['procedure']), $args);
		 }
	  }  
	  return $s;
	  
   }
}

//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
class ObjectProxy {

   private $sys_event_manager;
   private $proxy_object;
   
   public function __construct($sys_em, $object) {
   
      $this->sys_event_manager = $sys_em;
	  $this->proxy_object = $object;
	  
   }
   private function clean_obj($a) {
      if (is_array($a))
	     return 'Array';
	  else if (is_object($a))
	     return 'Object';
	  else
	     return htmlentities($a);
   }
   //---------------------------------------------------------
   public function __get($var) {
   
      return $this->proxy_object->$var;
   }
   //---------------------------------------------------------
   public function __set($var, $value) {
   
      $this->proxy_object->$var = $value;
   }   
   //---------------------------------------------------------
   public function __call($cmd, $args) {
      
	  $this->sys_event_manager->plusIndent(40);
	  
	  $this->sys_event_manager->dispatchEvent('proxy_before_call', 
	       'invoked method '.get_class($this->proxy_object).'->'.$cmd, 'z>arguments:',
		   array_map(array($this, 'clean_obj' ),$args));
		   
	   $this->sys_event_manager->plusIndent(40);
	   
	  $result = call_user_func_array(array($this->proxy_object, $cmd), $args);
	  
	  $this->sys_event_manager->minusIndent(40); 
	  
	  $this->sys_event_manager->dispatchEvent('proxy_after_call', 
	      'finished method '.get_class($this->proxy_object).'->'.$cmd, 'result:'.
		  ''/*$this->clean_obj($result)*/);
		  
	  $this->sys_event_manager->minusIndent(40);  
	  
	  return $result;
   }

}

//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
abstract class AbstractFactory {

   public final function get($className, $sm) {

	 $object =$this->in_get($className);  
	 return $object;
  }
  
  abstract protected function in_get($className);

}
//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
class stdFactory extends AbstractFactory {

  public function in_get($className) {
  
	 $object = new $className();
	 return $object;
  }
}
//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
class debugFactory extends AbstractFactory {

   private $sys_event_manager;
   
   public function __construct($sys_em) {
      $this->sys_event_manager = $sys_em;
   }

   public function in_get($className) {
   
      $this->sys_event_manager->dispatchEvent('proxy_before_create', 'started creating object: '.$className);
	  
      $object = new ObjectProxy($this->sys_event_manager, new $className());
	  
	  $this->sys_event_manager->dispatchEvent('proxy_after_create', 'finished creating object: '.$className);
	  
	  return $object;
   }
}








?>