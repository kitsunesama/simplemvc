<?php

class stdLayout implements IInjectable {

   protected $services = array();
   protected $template;
   
   protected $blocks = array();
  

   //--------------------------------------------------------------
   public function inject($serviceName, $service) {
   
      $this->services[$serviceName] = $service;
   }
   
   //--------------------------------------------------------------
   public function setTemplate($template) {
  
      $this->template = $template;	 
   }
   //--------------------------------------------------------------
   public function setBlock($blockName,$a) {
   
      $this->blocks[$blockName] = $a;
   }
   //--------------------------------------------------------------
   public function render() {
      extract($this->blocks);
	  
	  ob_start();
	  include $this->template;
	  
	  $v = ob_get_clean();
	  
	  return $v;
   }
   
   
}











?>