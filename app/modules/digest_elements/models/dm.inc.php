<?php

class DigestElementsModel extends AbstractModel {


   //----------------------------------------------------------------------
   protected function configDbMapping() {
         
	   //---------------------------------------------------
	  $this->setTable('digests_elements');
	  $this->setIdField('element_id');
	  $this->setOrderString('element_page_order');
	   //---------------------------------------------------
	  $this->addFields (array(
	  
	  //-------------------------------
	  'element_rss_name' => array (
	  
	     'type' => 's',
		 'caption' => 'Name of RSS Feed',
		 'hint' => 'enter the name you specified for RSS feed in RSS manager',
		 'input_type' => 'text',
		 'show_in_list' => true
	  ),
	  //-------------------------------
	  'element_page_order' => array (
	  
	     'type' => 'i',
		 'caption' => 'Element order',
		 'hint' => 'specify the order in which this part will be printed in post',
		 'input_type' => 'text',
		 'show_in_list' => true
	  ),
	  //-------------------------------
	  'element_request_period' => array (
	     
		 'type' => 'i',
		 'caption' => 'Request period in days',
		 'hint' => 'specify the period in days for which feed content would be retrieved<br/>(starting back from the day of publication)',
		 'input_type' => 'text',
		 'show_in_list' => true
		 
	  ),
	  //-------------------------------
	  'element_template' => array (
	  
	     'type' => 's',
		 'caption' => 'PHP template for each element',
		 'hint' => 'specify the PHP template which will be used to render each feed item<br/>use the conditional tags %title%, %description%, %link%, %pubdate% to operate with  <br/> use echo for output (See the example in help for more details',
		 
		 'input_type' => 'textarea',		
		 'show_in_list' => false					  
	  ),
	  //-------------------------------
	  'element_before_template' => array (
	  
	     'type' => 's',
		 'caption' => 'html template before elements',
		 'hint' => 'Input html which will be printed before output of the RSS feed content',
		 
		 'input_type' => 'textarea',		
		 'show_in_list' => false					  
	  ),
	  //-------------------------------
	  'element_after_template' => array (
	  
	     'type' => 's',
		 'caption' => 'html template after elements',
		 'hint' => 'Input html which will be printed after output of the RSS feed content',	 
		 'input_type' => 'textarea',	
		 'show_in_list' => false					  
	  ),
	  //-------------------------------
	  'digest_id' => array (
	     
		 'type' => 'i',		  
		 'input_type' => 'hidden',
		 'show_in_list' => false
	  )
    ));
   }
   
   
   //----------------------------------------------------------------------
   //----------------------------------------------------------------------
   protected function configValidators() {  
	  
	 
	   //---------------------------------------------------
	   $this->clientside_validator->add_rules( array(
	      
		  'element_rss_name' => array (
		     array('function'=>'function(inel) {var re = /^[\\\s]*$/; return !re.test(inel);}',
			       'message'=>'rss name cannot be empty string')
		  ),
		  'element_page_order' => array (
		     array('function'=>'function(inel) {var re = /^\\\d+$/; return re.test(inel);}',
			       'message'=>'this should be a number'	   
			 )
		  ),
		  'element_request_period' => array (
		     array('function'=>'function(inel) {var re = /^\\\d+$/; return re.test(inel);}',
			       'message'=>'this should be a number'	   
			 )
		  )
	   
	   ));
	  
	  
	  
   }
}








?>