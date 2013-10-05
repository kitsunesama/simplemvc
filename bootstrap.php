<?php

   define ('DS',DIRECTORY_SEPARATOR);
   define ('DEBUG',isset($_GET['debug']));
   define ('SYS_LOGGER','ScreenTrasser');
   define ('SYS_LOGGER_PATH', dirname(__FILE__).DS.'system'.DS.'vendor'.DS.'trasser.inc.php');
   
   date_default_timezone_set('Europe/Moscow');
   
   require dirname(__FILE__).DS.'system'.DS.'sm.inc.php';
   
   //test
   $sm = new ServiceManager(array('location'=>dirname(__FILE__),'ds'=>DS));
   $sm->init();
   
   $em = $sm->getService('EventManager');
   $em->dispatchEvent('bootstrap_init', $sm, $em);
   
   $sm->ss('bootstrap_route','h>Bootstrap: start routing procedure');
   
   $em->dispatchEvent('bootstrap_before_route', $sm, $em);
   $sm->route();
   $em->dispatchEvent('bootstrap_after_route', $sm, $em, $sm->getRouteInfo());
   
   $sm->ss('bootstrap_dispatch','h>Bootstrap: start dispatching procedure');
   $controllers = $sm->getControllers();
   
   if ($controllers) {
      foreach ($controllers as $c) {
   
         $em->dispatchEvent('bootstrap_before_dispatch', $sm, $c, $sm->getRouteInfo());
         $c->dispatch();
         $em->dispatchEvent('bootstrap_after_dispatch', $sm, $c, $sm->getRouteInfo());
      }
   } else {
      $sm->ss('bootstrap_no_controllers_matched', 
	  'e>Bootstrap: No controllers matching current route');
   }

   $sm->ss('bootstrap_render','h>Bootstrap: start rendering process');
   
   $layout = $sm->getLayout();
   $em->dispatchEvent('bootstrap_before_render', $sm, $layout );
   $v = $layout->render(); 
   $em->dispatchEvent('bootstrap_after_render', $sm, $v );
   
   $sm->ss('bootstrap_respond','h>Bootstrap: start responding process');
   
   $response = $sm->getService('Response');
	  
	//$response->setHeader('Content-type:text/html; charset=win-1251');
   $response->setPage($v); 
   $response->output();




?>