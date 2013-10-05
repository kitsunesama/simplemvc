<?php

class SefRouter implements IInjectable, IConfigurable {

   private $services = array();
   
   private $routes = array();
   


   public function config() {
      
	  $sm = $this->services['ServiceManager'];
	  $location = $sm->getAppConfigLocation();
	  
	  $path = $location.'routes.php';
	  
	  $sm->sys_em->dispatchEvent('router_config', '#a>looking for routes at:  '.$path);
  
	  include $path;
	  
	  foreach ($config_array as $key=>$value) {
	     $this->routes[$key] = $value;
	  }
	  
	  $sm->sys_em->dispatchEvent('router_finish_config','#z>registered routes:', $this->routes);
	 
	  
   }

   public function get_info() {
   
        $sm = $this->services['ServiceManager'];
		$sm->sys_em->dispatchEvent('router_route', '#a>router: perform routing ');
        $request = $sm->getService('request');
	
	    $url = $request->get_url();
		$sm->sys_em->dispatchEvent('router_route_match', '#a>router: matching route for url: '.$url);
		
		foreach ($this->routes as $route) {
		   if (preg_match('/'.$route['route'].'/',$url, $matches)) {
		      
			  $controller = $route['controller'];
			  $action = $route['action'];
			  
			  if (isset($route['arguments'])) {
			  
			     $arguments = array();
				 
			     foreach ($route['arguments'] as $arg=>$narg) {
				    $arguments[$arg] = $matches[$narg];
				 }
			  }
			 $inf = compact("controller","action","arguments")
			 return $inf;
		   }
		}
		$sm->sys_em->dispatchEvent('routing_failed', '#a>router: matching route failed');
		return false;
   }

   
   public function inject($serviceName, $service) {
      $this->services[$serviceName] = $service;
   }
   
   
}
























?>