<?php

   $config_array = array (
   
     'route_build_template' => 'digest_element_action',
   
     'digest_element_action' => array (
	     'route' => 'digest_element_action=[:action]',
		 'controller' => 'DigestElementsController',
		 'action' => '[:action]',

                 'redirects' => array (
                    'insert'=>'index',
                    'update'=>'index',
                    'delete'=>'index')
	  ),
      'digest_element_default' => array (
	     'route' => 'digest_action=view',
		 'controller' => 'DigestElementsController',
		 'action' => 'index'
	  )
	  
	
	  
   )


?>