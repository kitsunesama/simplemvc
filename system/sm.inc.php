<?php

class ServiceManager {

   const sys_dir = 'system';
   
      const sys_services_dir = 'services';
   
   const app_dir = 'app';
   
      const config_dir = 'config';
	  
	     const classes_config_file = 'classes.config.php';
	     const services_config_file = 'services.config.php';
		 const application_config_file = 'app.config.php';
		 const events_config_file = 'events.config.php';
		 const routes_config_file = 'routes.config.php';
		 const templates_config_file = 'templates.config.php';
	  
	  const classes_dir = 'classes';
	  const vendor_dir = 'vendor';
	  const templates_dir = 'templates';
   
   const modules_dir = 'modules';
      
	  const controllers_dir = 'controllers';
	  const models_dir = 'models';
	  const views_dir = 'views';
	  
  
   
   private $sys_em;

   private $location;
   private $ds;
   
   private $classLoadFactory;
   private $layout;
   private $view_helper;
   
   
   private $application_config = array();
   
   private $registered_classes = array();
   private $registered_services = array();
   private $registered_events = array();
   private $registered_routes = array();
   private $registered_templates = array();
   
   private $route_info = array();

   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function __construct($base_init) {
      
	  if ((isset($base_init)) && (is_array($base_init))) {
	  
	     $this->ds = isset($base_init['ds']) ? $base_init['ds'] : '/';
	     $this->location = isset($base_init['location']) ? $base_init['location'] : dirname(__FILE__).$this->ds.'..';
	  }
   }
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   private function path_repl($path) {
   
      return str_replace('/',$this->ds, $path);
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   private function up_path($file) {
   
      return realpath(dirname($file). $this->ds. '..'. $this->ds). $this->ds;
   }   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   private function consts($file = __FILE__) {
      
	 $inf = array(); 
     $inf['sys_dir'] = $this->location. $this->ds. ServiceManager::sys_dir. $this->ds;
     $inf['sys_services_dir'] = ServiceManager::sys_services_dir. $this->ds;
   
     $inf['app_dir'] = $this->location. $this->ds. ServiceManager::app_dir. $this->ds;
     $inf['config_dir'] = ServiceManager::config_dir. $this->ds;
	 $inf['classes_dir'] = ServiceManager::classes_dir. $this->ds;
	 $inf['vendor_dir'] =  ServiceManager::vendor_dir. $this->ds;
	 $inf['templates_dir'] = ServiceManager::templates_dir. $this->ds;
     $inf['modules_dir'] = ServiceManager::modules_dir. $this->ds;
      
	 $inf['controllers_dir'] = ServiceManager::controllers_dir. $this->ds;
	 $inf['models_dir'] = ServiceManager::models_dir. $this->ds;
	 $inf['views_dir'] = ServiceManager::views_dir. $this->ds;
	 
	 $inf['classes_config_file'] = ServiceManager::classes_config_file;
	 $inf['services_config_file'] = ServiceManager::services_config_file;
     $inf['application_config_file']  = ServiceManager::application_config_file;
	 $inf['events_config_file'] = ServiceManager::events_config_file;
	 $inf['routes_config_file'] = ServiceManager::routes_config_file;
	 $inf['templates_config_file'] = ServiceManager::templates_config_file;
	 
	 $inf['inf_path'] = $this->up_path($file);
	 $inf['ds'] = $this->ds;
	
	  
	 return $inf;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function ss() {
   
      $args = func_get_args();
	  call_user_func_array(array($this->sys_em, 'dispatchEvent'), $args);
   }   


   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function init() {
   
      extract($this->consts());
      require_once ($sys_dir. 'sys.inc.php');
      $this->sys_em = new SysEventManager();		  
	  
      if (DEBUG==true) {
	     $loggerClass = SYS_LOGGER;
         require_once(SYS_LOGGER_PATH);	
		 
		 $logger = new $loggerClass();	
         $this->debug_init($this->sys_em, $logger);		 
	  }   

      $this->configSystem();
	  $this->configApp();
	  $this->configModules();
 
	  $this->initDefaultServices();
   }
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function route() {
   
      $this->ss('routing_start','h>matching current route:');
   
      $router = $this->getRouter();
	  
	  $inf = $router->get_info();
	  $this->ss('router_info','h>received routing information:', $inf);
	  
	  $this->setRouteInfo ( $inf );
   }

   
   //-----------------------------------------------------------------------
   //----------------------------------------------------------------------- 
   private function debug_init($sys_em, $logger) {
      
	  $sys_em->addEventListener('*',$logger,'write');
	  	
	  
   }
   
   //-----------------------------------------------------------------------
   //----------------------------------------------------------------------- 
   private function config_section ($section, $section_dir) {
   
      $this->ss('config_section_start','h>start registering '.$section.' section');
	  
	  extract($this->consts());
	  
	  $this->register_classes( $section_dir. $config_dir. $classes_config_file ,  $section);
	  $this->register_services( $section_dir. $config_dir. $services_config_file,  $section);
	  $this->register_events( $section_dir. $config_dir. $events_config_file,  $section);
	  $this->register_routes( $section_dir. $config_dir. $routes_config_file,  $section);
	  $this->register_templates( $section_dir. $config_dir. $templates_config_file,  $section);
  
	  $this->ss('config_section_finish', 'finished registering '.$section,
	  'z>registered '.$section.' classes',$this->registered_classes[$section],
	  'z>registered '.$section.' services', $this->registered_services[$section],
	  'z>registered '.$section.' events', $this->registered_events[$section],
	  'z>registered '.$section.' routes', $this->registered_routes[$section],
	  'z>registered '.$section.' templates', $this->registered_templates[$section]
	  );
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   private function configSystem() {
      
	  $this->ss('config_system_start', 
	  'h>start configuring system classes/services/events/routes/templates');
	  
	  extract($this->consts());
      $this->config_section('system', $sys_dir);

   }      
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   private function configApp() {
   
      extract($this->consts());
	   //-----------------------------------------------
	  $path = $app_dir. $config_dir. $application_config_file;
	  
	  $this->ss('config_app_config_start', 'h>start reading application configuration', 
	  'looking for configuration parameters at: '.$path);
	  
      if (file_exists($path)) {
	     include $path;		 
	 
		 foreach ($config_array as $key=>$val) {
		    $this->application_config[$key] = $val;		
		 }
	  }  	 
	  $this->ss('config_app_config_finish', 
	  'z>current configuration: '/*, $this->application_config*/);
						  
	  //-----------------------------------------------
	  $this->ss('config_application_start', 
	  'h>start configuring application classes/services/events/routes/templates');
	  
	  $this->config_section('application', $app_dir);
	  
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   private function configModules() {
   
      extract($this->consts());
   
      $dirName = $app_dir. $modules_dir;
   
      $this->ss('config_modules_start',
	  'h>start registering modules at: '.$dirName);
	  
	  $dir4 = opendir($dirName);   
	  while ($dir = readdir($dir4)) {
	     if (($dir=='.') || ($dir=='..') || (!is_dir($dirName.$dir)))
		    continue;
			
	     $this->ss('found_module','found module: '.$dir);
	
		 $this->config_section($dir, $dirName. $dir. $this->ds);	
	  }
	  closedir($dir4);
	  
	  $this->ss('config_modules_finish','finished registering modules');
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   private function register_part($part, $partvar, $configFile, $section) {
   
   
   	  $this->ss('register_part_start','looking for '.$part.'['.$partvar.'] for '.$section.' at '.$configFile);
	  
	  $ref = & $this->$partvar;
	  $ref[$section] = array();
	  
	  if (!file_exists($configFile)) {
	     $this->ss('file_not_found','e>file '.$configFile.' not found');	 
	     return false;
		 }
	  
      extract($this->consts($configFile));
      include $configFile;

	  foreach ($config_array as $key=>$val) {     
	     $ref = & $this->$partvar;
         $ref[$section][$key] = $val;		 
	  }
   }   
   //-----------------------------------------------------------------------   
   private function register_classes($configFile, $section) {
   
      $this->register_part('classes', 'registered_classes', $configFile, $section);
   } 
   //-----------------------------------------------------------------------   
   private function register_services($configFile, $section) {
   
      $this->register_part('services', 'registered_services', $configFile, $section);
	  
	  if (!isset($this->registered_services[$section]))
	     return;
		 
      if (!isset($this->registered_classes[$section])) {
		 $this->registered_classes[$section] = array();   
		 } 
	  foreach ($this->registered_services[$section] as $service) {
         $this->registered_classes[$section][$service['classname']] = $service['classpath'];
	  }
   }
   //----------------------------------------------------------------------- 
   private function register_events($configFile, $section) {
   
      $this->register_part('events', 'registered_events', $configFile, $section);
   
   }     
   //----------------------------------------------------------------------- 
   private function register_routes($configFile, $section) {
   
      $this->register_part('routes', 'registered_routes', $configFile, $section);
   }  
   //----------------------------------------------------------------------- 
   private function register_templates($configFile, $section) {
   
      $this->register_part('templates', 'registered_templates', $configFile, $section);
   }   
   
   //-----------------------------------------------------------------------
   //----------------------------------------------------------------------- 
   private function initDefaultServices() {
   
      extract ($this->consts());
	  spl_autoload_register(array($this, 'classLoader'));
      //------------------------------------
      if (DEBUG==false) {  
	     $this->classLoadFactory = new stdFactory();
	  } else {
	     
	     $this->classLoadFactory = new debugFactory($this->sys_em);
		 }
	  $this->ss('class_factory_define', 
	  'h>defined class factory: '.get_class($this->classLoadFactory));
	
      //------------------------------------
	  $this->ss('configuring_router', 
	  'h>configuring router');
	  
	  $router = $this->getRouter();
	  $router->init($this->registered_routes);
	  
	  //------------------------------------
      $this->ss('attach_registered_events', 'h>start attaching registered events');  	  
	  $em = $this->getService('EventManager','system');
	  
	  foreach ($this->registered_events as $key => $section) {
	  
	      $this->ss('attach_registered_events_section', 
		  'h>start attaching registered events in section: '.$key);  
		  
	     foreach ($section as $event=>$val) {
		
		     $this->ss('attach_registered_events_event', 
		     'h>attaching event: '.$event, $val); 
		
			$obj = $this->getService($val['servicename'], $key);
		    $em->addEventListener($event, $obj, $val['method']);		 
	     }
	  }
	  //------------------------------------
	  
      $this->ss('set_global_layout', 'h>setup global layout');  
      $this->setLayout( $this->getService('stdLayout','system') );
	  
	  $this->ss('set_global_view_helper', 'h>setting global view helper / layout template');
	  
	  if (!$this->get_config('debug_mode')) {
	  
	     $viewHelper = $this->getService('stdViewHelper','system');
		 
		 if ($templateName = $this->get_config('layout_template')) {
		    $template = $this->getTemplate($templateName, 'application');
		 } else {
		    $template = $this->getTemplate('system_standart_layout','system');
		 }
		 
	  } else {
	  
	     $viewHelper = $this->getService('debugViewHelper','system');
		 $template = $this->getTemplate('system_debug_layout','system');
	  }
	  
	  $this->setViewHelper ( $viewHelper );
  
	  $this->getLayout()->setTemplate($template);
	  
   }   
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function get_config($key) {
      return isset($this->application_config[$key]) ? $this->application_config[$key] : false;
   }
 
   //----------------------------------------------------------------------- 
   public function setLayout($layout) {
   
      $this->layout = $layout;
   }
   //-----------------------------------------------------------------------
   public function getLayout() {
   
      return $this->layout;
   }   
   //-----------------------------------------------------------------------
   public function setViewHelper($helper) {
      
	  $this->view_helper = $helper;
   }
   
   //-----------------------------------------------------------------------
   public function getViewHelper() {
   
      return $this->view_helper;
   }
   
   //-----------------------------------------------------------------------
   public function setRouteInfo($routeInfo) {
   
      $this->route_info = $routeInfo;
   }
   //-----------------------------------------------------------------------
   public function getRouteInfo() {
   
      return $this->route_info;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function getTemplate($templateName, $defaultSection = 'system') {
   
      if (array_key_exists($templateName, $this->registered_templates[$defaultSection])) {
	  
	     $path = $this->registered_templates[$defaultSection][$templateName];
		 
	     $this->ss('template_found', 'found in specified section: '.$defaultSection, 
		 'found template '.$templateName.' at : '.$path);
	  
	     return $path;
		
		 }
	  else {	 
	     foreach ($this->registered_templates as $section) {
	        if (array_key_exists($templateName, $section)) {			
					 
			   $this->ss('template_found', 'found in section: '.$section,
			   'found template  '.$templateName.' at : '.$templateName);	 
		
		       return $section[$templateName];   
			}   		
	     }
	  }
	  
	  $this->ss('template_not_found','e>template  '.$templateName.' is not registered');
      return false;	  
   }

   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   private function classLoader($className)  {
       
	  $this->ss('class_extends','g>looking for class '.$className);
  	 
	  foreach ($this->registered_classes as $section) {
	        if (array_key_exists($className, $section)) {
			
			   $this->ss('class_require', 
			   'g>require class '.$className.' from : '.$section[$className]);
			
		       require_once $section[$className];
			   return true;
			}   		
	  }
	 $this->ss('service_class_not_found', 
			         'e>service parent class not found / class: '.$className);
 
	 return false;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function getService($serviceName, $section='system') {
   
      $this->ss('service_request', 'h>requested service: '.$section. ' / '. $serviceName);
	    
	
      if (isset($this->registered_services[$section][$serviceName])) {
  
	     $service_info = & $this->registered_services[$section][$serviceName];
		 
		 
		 //------------------------------------
		 $this->ss('class_require', 'service found, info: ', 
		          array_diff_key($service_info, array('loaded'=>true)));
		
		 //------------------------------------	 
		 if (isset($service_info['singleton']) && (isset($service_info['loaded'])) ) {
		 
		       $this->ss('service_return_singleton','g>return previously created singleton: '.$serviceName);		 
			   $service = $service_info['loaded'];
			   return $service;
			} 
		 else {	 
		 
		    $this->ss('service_object_create','start creating object for: '.$serviceName);
		 
		    $service = $this->classLoadFactory->get($service_info['classname'], $this);
			}
		 
		  $service_info['loaded'] = $service;
		  
		  //------------------------------------
	      $refl = new ReflectionClass($service_info['classname']);	
		  //------------------------------------
	      if ($refl->implementsInterface('IInjectable')) {
		  
		  	  $this->ss('service_inject_sm','start injecting sm into '.$serviceName);
		  
	          $service->inject('ServiceManager',$this);
	      }
		  //------------------------------------
		  if ($refl->implementsInterface('IConfigurable')) {
		  
		     $this->ss('service_configure','start configuring '.$serviceName);
		  
		     $service->config();
		  }
		  //------------------------------------
		  return $service;

		 
	  } else {
	  
	    $this->ss('service_not_registered','e>failed to find registered service: '.$serviceName);
	    return false;
	 }
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function getRouter() {
   
      $this->ss('router_requested','h>requested router');
	  
      if ($this->get_config('sef')==true) {
	  
	     return $this->getService('SefRouter','system');
	  } else {
	  
	     return $this->getService('Router','system');
	  }
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function getControllerWithArgs($params, $getdata, $postdata) {
   
      $this->ss('controller_request_with_args','h>requested controller with args: ',
	  $params, $getdata, $postdata);
   
      $controller = $this->getService($params['controller'], $params['module']);
	  $controller->setModule( $params['module'] );
	  $controller->setName( $params['controller'] );
	  $controller->setAction( $params['action'] );
	  $controller->setRouteBuildTemplate( $params['route_build_template']);
	  $controller->setArguments( $getdata );
	  $controller->setPostData( $postdata );
	  
	  $controller->init();
	  
	  return $controller;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function getControllers() {
   
      $this->ss('controller_request','h>requested controller for current route');
   
      $route_info = $this->getRouteInfo();
	  
	  if (!isset($route_info['controllers'])) {
	     return false;
	  }
	  $getdata = $route_info['arguments'];
	  $postdata = $route_info['postdata'];
	  
	  $controllers = array();
	  foreach ($route_info['controllers'] as $params) {
	  
	     $controllers[] = $this->getControllerWithArgs($params, $getdata, $postdata);
	  }

	  return $controllers;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function getView($viewName, $moduleName) {
   
      $this->ss('view_request','h>requested view '.$moduleName.' / '.$viewName);  

	  $view = $this->getService($viewName, $moduleName);

	  $view->setHelper($this->getViewHelper()); 
	  $view->setLayout($this->getLayout());  
	  
      return $view;	  
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function getModel($modelName, $moduleName) {
   
      $this->ss('model_request', 'h>requested model '.$moduleName.' / '.$modelName);
	  
      $model = $this->getService($modelName, $moduleName);
	  
	  $this->ss('select_db_adapter_strategy','h>sm: selecting db adapter strategy');
	  
	  if ($db_adapter_strategy = $this->get_config('db_adapter_strategy')) {
	  
	     $this->ss('application_db_adapter_strategy', 
		 'for application specified db adapter retrieving strategy: '.$db_adapter_strategy);
		 
	     $strategy = $this->getService($db_adapter_strategy, 'application');	     
	  } else {
	  
	     $this->ss('default_db_adapter_strategy',
		 'using default db adapter retrieving strategy: std_dbAdapter_strategy');
	  
	     $strategy = $this->getService('std_dbAdapter_strategy', 'system');
	  }
	  $model->setDbAdapterStrategy($strategy); 
	  
	  $recs_per_page = $this->get_config('records_on_page');
      $this->ss('models_records_per_page', 
	  'for all models in application specified records_per_page: '.$recs_per_page);	  
	  
	  $model->setRecsPerPage( $recs_per_page ? $recs_per_page : 10);
	  
	  $model->init();
	  
	  return $model;
   }
   
   
}















?>