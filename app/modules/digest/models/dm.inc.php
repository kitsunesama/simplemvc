<?php

class DigestModel extends AbstractModel {


   //----------------------------------------------------------------------
   protected function configDbMapping() {
         
	   //---------------------------------------------------
	  $this->setTable('digests');
	  $this->setIdField('digest_id');
	  $this->setOrderString('digest_id desc');
	   //---------------------------------------------------
	  $this->addFields (array(
	  
	  //-------------------------------
	  'digest_title' => array (
	  
	     'type' => 's',
		 'caption' => 'Publication title',
		 'hint' => 'Specify the text which will be used as title of publication',
		 'input_type' => 'text',
		 'show_in_list' => true
	  ),
	  //-------------------------------
	  'digest_text' => array (
	  
	     'type' => 's',
		 'caption' => 'Publication text',
		 'hint' => 'Specify the text which will be printed at the top paragraph of publication.<br/>(You can use html)',
		 'input_type' => 'textarea',
		 'show_in_list' => false
	  ),
	  //-------------------------------
	  'digest_schedule_period' => array (
	     
		 'type' => 'i',
		 'caption' => 'Publishing period (days)',
		 'hint' => 'Specify the interval in days between publications',
		 'input_type' => 'text',
		 'show_in_list' => true
		 
	  ),
	  //-------------------------------
	  'digest_schedule_time' => array (
	  
	     'type' => 's',
		 'caption' => 'Publishing time',
		 'hint' => 'Specify the time of day when you want the post to be published',
		 
		 'input_type' => 'select',
		 'select_values' => array('2:00','7:00','12:00','16:00','19:00','23:00'),
		 'select_captions' => array('night (2:00)','morning (7:00)', 'afternoon (12:00)',
                                      'day (16:00)', 'evening (19:00)', 'late evening (23:00)'),
		 'show_in_list' => true						  
	  ),
	  //-------------------------------
	  'digest_is_active' => array (
	     
		 'type' => 'i',
		 'caption' => 'Is publication active',
		  
		 'input_type' => 'select',
		 'select_values' => array(1,0),
		 'select_captions' => array('Active','Inactive'),
		 'show_in_list' => true
	  ),
	  //-------------------------------
	  'digest_date' => array (
	     
		 'type' => 's',
		 'caption' => 'published: ',		 
		 'input_type' => 'none',
		 'show_in_list' => false
	  ),
	  //-------------------------------
	  'digest_post_type' => array (
	     
		 'type' => 's',
		 'caption' => 'Post type',
		 'hint' => 'Specify the post type for publications',
		 
		 'input_type' => 'select',
		 'select_values' => array(''),
		 'select_captions' => array(''),
		 'show_in_list' => true
		 
	  ),
	  //-------------------------------
	  'digest_taxonomy' => array (
	     
		 'type' => 's',
		 'input_type' => 'hidden',
		 'show_in_list' => false
	  ),
	  //-------------------------------
	  'digest_category_id' => array (
	  
	     'type' => 'i',
		 'caption' => 'Category',
		 'hint' => 'Specify the category for publications',
		 
		 'input_type' =>'select',
		 'select_values' => array(0),
		 'select_captions' => array(0),		
         'show_in_list' => true		 
		 
	  )
	  
	  ));
   }
   //----------------------------------------------------------------------
   //----------------------------------------------------------------------
   protected function getAutoFields() {
   
      return array( 'digest_date' => date('Y-m-d H:i:s'));
   }

   
   //----------------------------------------------------------------------
   //----------------------------------------------------------------------
   protected function configValidators() {  
	  
	  $this->serverside_replacements->add_rules( array(
	  
	     'digest_title' => array (
		       array ('function'=> 'return strip_tags($a);'),
			   array ('function'=> 'return trim($a);')
		 )	 
	  ));
	   //---------------------------------------------------
	  $this->serverside_validator->add_rules( array(
	     
		 'digest_title' => array (
		     array ('function'=>'return !empty($a);', 
			        'message'=>'title cannot be empty, contain tags or to be empty string')
		 )
	  ));  
	   //---------------------------------------------------
	   $this->clientside_validator->add_rules( array(
	      
		  'digest_title' => array (
		     array('function'=>'function(inel) {var re = /^[\\\s]*$/; return !re.test(inel);}',
			       'message'=>'title cannot be empty string')
		  ),
		  'digest_schedule_period' => array (
		     array('function'=>'function(inel) {var re = /^\\\d+$/; return re.test(inel);}',
			       'message'=>'this should be a number'	   
			 )
		  )
	   
	   ));
	  
	  
	  
   }
}








?>