<?php

class AppDbAdapterStrategy implements IInjectable, IConfigurable {

   private $services = array();
   private $sm;

   //----------------------------------------------------
   public function inject($serviceName, $service) {
   
      $this->services[$serviceName] = $service;
   }
   public function config() {
      
	  $this->sm = $this->services['ServiceManager'];
   }
   //----------------------------------------------------
   
   public function getDbAdapter() {
   
      $this->sm->ss('strategy_get_db_adapter', 'h>strategy: retrieving Db Adapter for model');
      if ($this->sm->get_config('dbadapter')=='wp') {
	  
	     $wpadapter = $this->sm->getService('wp_adapter');
		 $wpadapter->requireWP();

	     $dbModel = $this->sm->getService('dbAdapter_wp','application');	 
	     return $dbModel;
	  }
	  else {
	     if ($this->sm->get_config('dbadapter')=='mysqli') {
		 
			$dbModel = $this->sm->getService('dbAdapter_mysqli','application');
		    $dbConn = $this->sm->getService('dbConn_mysqli','application');
		 
		    $server_name = $this->sm->getService('Request','system')->get_server();
		    if (strpos($server_name,'hostenko.com')!==false) {
			   $cred_section = 'credentials_hostenko';
			} else if (strpos($server_name,'dmkim1979.ru')!==false) {
			   $cred_section = 'credentials_sprinthost';
			} else {
			   $cred_section = 'credentials_beget';
			}
			$this->sm->ss('strategy_config_db_adapter',
			'h>strategy: configuring db_connection for mysqli Db Adapter ',
			'credentials section: '.$cred_section);
			
		    $dbConn->setConnParams($this->sm->get_config($cred_section)); 
		    $dbModel->setup($dbConn);
			
			return $dbModel;		
		 
		 } else 
		    {
			   $dbModel = $this->getService('dbAdapter', 'system');
			   return $dbModel;
			   

			
		 }
	  }
   }
}
















?>