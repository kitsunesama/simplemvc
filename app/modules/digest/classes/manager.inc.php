<?php

//------------------------- old
class DigestManager implements IInjectable {
   
   private $services;
   private $trasser;
   
   //-------------------------------------------------------
   public function onBootstrapAfterRoute($serviceManager, $eventManager, $route_info) {
      
	  
	   
	  $serviceManager->ss('event_caught_DigestManager', 
	  'h>executing event listener DigestManager->onBootstrapAfterRoute ',
	  'z>route info: ',$route_info);
  
	  
   }
   //-------------------------------------------------------
   public function onBeforeDispatch ($serviceManager, $controller, $route_info) {
      
	  $serviceManager->ss('event_caught_DigestManager', 
	  'h>executing event listener DigestManager->onBeforeDispatch ',
	  'z>route info: ',$route_info);
  
      $v = $controller->getView();
	  $v->setBlockName('content_block','content_master');
	  $v->setBlockName('message_block','message_master');
  
   }
   //-------------------------------------------------------
   public function onAfterDispatch ($serviceManager, $controller, $route_info) {
   
      $serviceManager->ss('event_caught_DigestManager', 
	  'h>executing event listener DigestManager->onAfterDispatch ',
	  'z>route info: ',$route_info);
	  
      /*if (($controller->getName()=='DigestController') && ($route_info['action']=='view')) {
	  
         $c = $serviceManager->getControllerWithArgs(
	        array(
		       'module' => 'digest',
			   'controller' => 'DigestDetailsController',
			   'action' => 'index',
			   'arguments' => array('digest_id'=> 
			      $route_info['arguments'][$controller->getDbKey()])
		    ));
	  	 $v = $c->getView();
		 $v->setBlockName('content_block','content_detail');
		 $v->setBlockName('message_block','message_detail');
	     $c->dispatch();
	  }*/
   }
   //-------------------------------------------------------
   public function onBeforeRender( $serviceManager, $layout) {
   
       $serviceManager->ss('event_caught_DigestManager', 
	  'h>executing event listener DigestManager->onBeforeRender ',
	  'z>route info: ',$route_info);
   
       $layout->setTemplate( $serviceManager->getTemplate('digest_standart_layout','digest'));
   }
   //-------------------------------------------------------
   public function inject($serviceName, $service) {
   
      $this->services[$serviceName] = $service;
   }
   
}












?>