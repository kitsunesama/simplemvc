<?php

   $config_array = array (
   
     'route_build_template' => 'digest_action',
    
   
     'digest_action' => array (
	     'route' => 'digest_action=[:action]',
		 'controller' => 'DigestController',
		 'action' => '[:action]',
                 'redirects' => array (
                    'insert'=>'index',
                    'update'=>'index',
                    'delete'=>'index',
                  )
	  ),
	  
      'digest_default' => array (
	     'route' => '[default]',
		 'controller' => 'DigestController',
		 'action' => 'index'
	  )
	  
	
	  
   )


?>