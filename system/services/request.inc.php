<?php

class Request implements IInjectable, IConfigurable {

   private $services = array();
   
   private $url;
   private $server_name;
   private $get_vars;
   private $post_vars;
   private $files;
   private $cookies;
   
   private $session_manager;
   
   
   public function config() {
   
      $this->server_name = $_SERVER['SERVER_NAME'];
      $this->url = $_SERVER['REQUEST_URI'];
	  $this->get_vars = is_array($_GET) ? $_GET : array();
	  $this->post_vars = is_array($_POST) ? $_POST : array();
	  $this->files = is_array($_FILES) ? $_FILES : array();
	  $this->cookies = is_array($_COOKIE) ? $_COOKIE : array();
	  
	  $this->session_manager = $this->services['ServiceManager']->getService('SessionManager');
	  
	  if ($this->session_manager) {
	     
		 //$this->session_manager->persist_get($this->get_vars);
	  }
   }
   
   public function get_server() {
      return $this->server_name;
   }
   public function get_url() {
      return $this->url;
   }
   
   public function get_all_get() {
      return $this->get_vars;
   }
   public function get_all_post() {
      return $this->post_vars;
   }
  
   public function get_get($key) {
      return isset($this->get_vars[$key]) ? $this->get_vars[$key] : false;
   }
   public function get_post($key) {
      return isset($this->post_vars[$key]) ? $this->post_vars[$key] : false;
   }
   
   public function Inject($serviceName, $service) {
   
      $this->services[$serviceName] = $service;
   }
}









?>