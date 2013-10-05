<?php

class stdValidator implements IInjectable {

   private $rules = array();

   protected $services = array();
   
   //----------------------------------------------------------
   public function inject($serviceName, $service) {
      
	  $this->services[$serviceName] = $service;
   }
   
   //----------------------------------------------------------
   public function add_rules($rules) {
      $this->rules = $rules;
   }
   //----------------------------------------------------------
   public function get_rules() {
      return $this->rules;
   }
   
   //----------------------------------------------------------
   public function validate($data) {
   
      $errs = array();
      foreach ($data as $key=>$value) {	  
         if (isset($this->rules[$key])) {                
            
             foreach ($this->rules[$key] as $check) {
			 
                $fn = create_function('$a',$check['function']);
		
				 if (call_user_func($fn, $value)==false) {            
                    $errs[] = $check['message'];
                 }
             }
         }
	  }
	  return implode('<br/>',$errs);
	  
	}
	  
	//----------------------------------------------------------  
	public function replace($data) {
	
	   foreach ($data as $key=>$value) {	  
         if (isset($this->rules[$key])) {                
            
             foreach ($this->rules[$key] as $func) {
			 
                $fn = create_function('$a',$func['function']);
                $value = call_user_func($fn, $value);
             }
			 $data[$key] = $value;
          }
	   }
	   return $data;
	}
	  
                     

}




















?>