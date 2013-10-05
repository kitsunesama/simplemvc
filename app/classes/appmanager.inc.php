<?php

class ApplicationManager implements IInjectable {


  //-------------------------------------------------------
   public function inject($serviceName, $service) {
   
      $this->services[$serviceName] = $service;
   }
 //-------------------------------------------------------
   public function onBootstrapAfterRoute($serviceManager, $eventManager, $route_info) {
      
	  
	   
	  $serviceManager->ss('event_caught_ApplicationManager', 
	  'h>executing event listener ApplicationManager->onBootstrapAfterRoute ',
	  'z>route info: ',$route_info);
	  
	  foreach ($route_info['controllers'] as $controller_info) {
   
         if (in_array($controller_info['module'], array('digest','digest_elements'))) {
	        $eventManager->addEventListener('bootstrap_before_dispatch', $this, 'onBeforeDispatch');
			break;
		 }
	  }
	  
   }
   //-------------------------------------------------------
   public function onBeforeDispatch ($serviceManager, $controller, $route_info) {
      
	  $serviceManager->ss('event_caught_ApplicationManager', 
	  'h>executing event listener ApplicationManager->onBeforeDispatch ',
	  'z>route info: ',$route_info);
  
      $v = $controller->getView();
	  
	  if ($controller->getName()=='DigestController') {
	  
	     $v->setBlockName('content_block','content_master');
	     $v->setBlockName('message_block','message_master');
	  } else
	     if ($controller->getName()=='DigestElementsController') {
		 
		    $v->setBlockName('content_block','content_detail');
	        $v->setBlockName('message_block','message_detail');
		 }
  
   }
}












?>