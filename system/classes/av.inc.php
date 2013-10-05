<?php

abstract class AbstractView implements IInjectable {

   protected $services = array();
   
   protected $helper;
   protected $layout;
   
   protected $variables = array();

   //-------------------------------------------------
   public function inject($serviceName, $service) {
      $this->services[$serviceName] = $service;
   }
   
   //-------------------------------------------------
   public function setHelper($helper) {
   
      $this->helper = $helper;
	  
   }

   
   //-------------------------------------------------
   public function setLayout($layout) {
   
      $this->layout = $layout;
   }
   
   //-------------------------------------------------
   public function set($name, $value) {
   
      $this->variables[$name] = $value;
   }
   //-------------------------------------------------
   protected function get($name) {
   
      return isset($this->variables[$name]) ? $this->variables[$name] : false;
   }
   //-------------------------------------------------
   protected function before_render($action) {
   }   
   
   //-------------------------------------------------
   public function render($action) {
   
      $this->before_render($action);
   
      if ((int)method_exists($this, 'view_'.$action)) {
	     call_user_func(array($this,'view_'.$action));	 
	  } else {
	     $this->view_notfound('view_'.$action);
	  }

   }
   
   //-------------------------------------------------
   protected function view_notfound($viewaction) {
   
      $a = $this->helper->drawMessage('action '.$viewaction.' doesn\'t exists');
  
	  $this->layout->setBlock('sys_notices4', $a);
  
   }
}










?>