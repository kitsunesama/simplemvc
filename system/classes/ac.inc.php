<?php

abstract class AbstractController implements IInjectable  {

   protected $module_name;
   protected $name;
   protected $action;
   protected $route_build_template;
   protected $arguments;
   protected $post_data;
   protected $view = null;
   
   protected $services = array();
   
   
   abstract function init();
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function inject($serviceName, $service) {
   
      $this->services[$serviceName] = $service;
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function setModule($moduleName) {
      $this->module_name = $moduleName;
   }
   public function getModule() {
      return $this->module_name;
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function setName($name) {
      $this->name = $name;
   }
   public function getName() {
      return $this->name;
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function setAction($action) {
      $this->action = $action;
   }
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function setRouteBuildTemplate($route_build_template) {
   
      $this->route_build_template = $route_build_template;
   }   

   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function setArguments($arguments) {
   
      $this->arguments = $arguments;
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function setPostData($postdata) {
   
      $this->post_data = $postdata;
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function getPostData() {
      return $this->post_data;
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------  
   public function setView($view) {
   
      $sm = $this->services['ServiceManager'];
	  $sm->ss('abstract_controller_set_view',
	  'h>abstract controller setting view '.get_class($view));
      $this->view = $view;
   }
   //----------------------------------------------------------------
   public function getView() {
      
	  return $this->view;
   }
 
   //----------------------------------------------------------------
   //---------------------------------------------------------------- 
   public function dispatch() {

      $sm = $this->services['ServiceManager'];
	  $sm->ss('abstract_controller_dispatch','start dispatching in abstract controller',
	  'controller: '.$this->module_name.' / '.$this->name.', action: '.$this->action);
   
      if ((int)method_exists($this, 'action_'.$this->action)) {
	  
	     $args = array ($this->arguments);
	     call_user_func_array(array($this, 'action_'.$this->action), $args);
		 
		 $this->render();
		 
	  } else {
	       $sm->ss('controller_action_does_not_exists',
		   'e>action '.$this->action.'doesn\'t exists in controller: '.
		   $this->module_name.' / '.$this->name);
      }	  
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------  
   public function render() {
   
      $sm = $this->services['ServiceManager'];
	  $sm->ss('abstract_controller_render','start rendering in abstract controller',
	  'controller: '.$this->module_name.' / '.$this->name.', action: '.$this->action);
	  
      if (!is_null($this->view)) {
         $this->view->render($this->action);
	  } else {
	     $sm->ss('view_not_attached_to_controller', 
		 'e>controller has not attached view to render',
		 'controller: '.$this->module_name.' / '.$this->name.', action: '.$this->action);
	  }
	  
   }
}





?>