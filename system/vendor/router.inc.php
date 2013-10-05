<?php

class Router implements IInjectable {

   private $services = array();
   private $registered_routes = array();
   
   private $url;
   private $arguments = array();
   private $post_data = array();
 
   //------------------------------------------------
   public function init($routes) {
   
      $this->registered_routes = $routes;
	  
	  $sm = $this->services['ServiceManager'];
	  $sm->ss('router_setup', 'h>router: setup router ');
	  
      $request = $sm->getService('Request', 'system' );
	  
	  $this->url = $request->get_url();
	  $sm->ss('router_url', 'h>router: requested uri: '.$this->url);
	  
	  $this->arguments = $request->get_all_get();
	  $sm->ss('router_arguments', 'h>router: request arguments: ',$this->arguments);
	  
	  $this->postdata = $request->get_all_post();
	  $sm->ss('router_postdata', 'h>router: post data: ',$this->postdata);
	  
   }
   //------------------------------------------------
   public function get_info() {
   
        $sm = $this->services['ServiceManager'];
		$sm->ss('router_route', 'h>router: perform routing ');

		$sm->ss('router_route_match', 'h>router: matching route for url: '.$this->url);
			
		$controllers = array();
		foreach ($this->registered_routes as $route_module=>$routes) {
		
		   $route_build_template = '';
		   
		   foreach ($routes as $key => $route) {
		   
		      //----------------------  route build template
			  if ($key=='route_build_template') {
			     $route_build_template = $route;
				 continue;
			  }
		   
		   	  //----------------------  default
			  if ($route['route']=='[default]') {
			     $controller = array();
			     $controller['module'] = $route_module;
				 $controller['controller'] = $route['controller'];
				 $controller['action'] = $route['action'];
				 $controller['route_build_template'] = $route_build_template;
				 
				 $sm->ss('router_default_match', 'using fallback route for module: '.$route_module);
				 
				 $controllers[] = $controller;
				 break;
			  }
		      //----------------------   match
		      $route_args = explode('&',$route['route']);
			  $found = true;
			  
			  $param_map = array(); 
			  // digest_action = [:action]  converts to  
			  //                '[:action]' => array ( 'digest_action', $_GET['digest_action'])
			  
			  foreach ($route_args as $route_arg) {
			  
			      $arr = explode('=', $route_arg);
			      $route_key = array_shift($arr);
				  $route_value = array_shift($arr);
			  
			      if (!array_key_exists( $route_key, $this->arguments)) {
				     $found = false;
					 break;
				  }
				  
				  if (substr($route_value,0,2)=='[:') {
				     $param_map[$route_value] = array($route_key, $this->arguments[$route_key]);
				  } else {
				     if ($route_value!=$this->arguments[$route_key]) {
					    $found = false;
						break;
					 }
				  }
			  }
			  //-------------------
			  if ($found) {
			  
			     $sm->ss('router_record_match', 'using route ['.$key.'] for module: '.$route_module);
				 
			     $controller = array();
				 $controller['module'] = $route_module;
				 $controller['route_build_template'] = $route_build_template;
				 
				 if (substr($route['controller'],0,2)=='[:')
				    $controller['controller'] = $param_map[$route['controller']][1];
				 else
				    $controller['controller'] = $route['controller'];
				 
				 if (substr($route['action'],0,2)=='[:')
				    $controller['action'] = $param_map[$route['action']][1];
				 else
				    $controller['action'] = $route['action'];	

				 $controllers[] = $controller;
				 
				 if (isset($route['redirects'])) {
				    $redirects = $route['redirects'];
					
					$action_argument = $param_map[$route['action']][0];
					$action = $param_map[$route['action']][1];
					
					if (array_key_exists($action, $redirects)) {
					   $this->arguments[$action_argument] = $redirects[$action];
					}
				 }
				 
				 break;	
			  }
			  //--------------------------
		   }
		}
		

	
	    $inf = array('controllers' => $controllers,
		             'arguments' => $this->arguments,
					 'postdata' => $this->postdata);
					 
		$sm->ss('router_routing_finish', 'h>router: routing complete, controllers: ', $inf);					 
					 
		return $inf;
			
   }
   //------------------------------------------------
   public function build_route($module, $route_build_template, $placeholders, $arguments) {
   
      $sm = $this->services['ServiceManager'];
	  
	  $sm->ss('router_build_route', 'h>router: building route ',
	  'module: '.$module. ' build_template: '.$route_build_template.
	  ' placeholders: '.implode(',',$placeholders), 'arguments:',$arguments);
	  
	  $current_url_args = $this->arguments;
	  
	  $route_template = $this->registered_routes[$module][$route_build_template];
	  
	  $route_args = explode('&', $route_template['route']);
	  
	  foreach ($route_args as $route_arg) {
	  
	     $arr = explode('=', $route_arg);
		 $route_key = array_shift($arr); 
		 $route_value = array_shift($arr);
		 
		 if (substr($route_value,0,2)=='[:') {
		    $route_value = array_shift($placeholders);
		 }
		 $current_url_args[$route_key] = $route_value;
	  }
	  foreach ($arguments as $key=>$value) {
	     $current_url_args[$key] = $value;
	  }
	  $sm->ss('route_built', 'z>route complete, route params: ', $current_url_args);

      return $_SERVER['PHP_SELF'].'?'.http_build_query($current_url_args);
   }

   //------------------------------------------------
   public function inject($serviceName, $service) {
      $this->services[$serviceName] = $service;
   }
   
   
}
























?>