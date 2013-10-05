<?php

$config_array = array (
   'EventManager' => array (
      'classname' => 'EventManager',
	  'classpath' => $inf_path. $sys_services_dir. 'em.inc.php',
	  'singleton' => true
   ),
   'Request' => array (
      'classname' => 'Request',
	  'classpath' => $inf_path. $sys_services_dir. 'request.inc.php',
	  'singleton' => true
   ),
   
   'SessionManager' => array (
      'classname' => 'SessionManager',
	  'classpath' => $inf_path. $sys_services_dir. 'session.inc.php',
	  'singleton' => true
   ),
   
   'Response' => array (
      'classname' => 'Response',
	  'classpath' => $inf_path. $sys_services_dir. 'response.inc.php',
	  'singleton' => true
   ),
   
   'Router' => array (
      'classname' => 'Router',
	  'classpath' => $inf_path. $vendor_dir. 'router.inc.php',
	  'singleton' => true
   ),
   
   'SefRouter' => array (
      'classname' => 'SefRouter',
	  'classpath' => $inf_path. $vendor_dir. 'sefrouter.inc.php',
	  'singleton' => true
	  
   ),
   
   //-------------------------------------
   'SysTrasser'=> array (
      'classname' => 'ScreenTrasser',
	  'classpath' => $inf_path. $vendor_dir. 'trasser.inc.php'
   ), 
 
   //-------------------------------------
   //    default functionality
   //-------------------------------------

   'std_dbAdapter_strategy' => array (

      'classname' => 'dbAdapterStrategy',
	  'classpath' => $inf_path. $vendor_dir. 'dbadapter_strategy.inc.php'
   ),
   //-------------------------------------  
   'dbAdapter' => array (
      'classname' => 'stub_dbAdapter',
	  'classpath' => $inf_path. $vendor_dir. 'stubdbadapter.inc.php'
   ),
   //-------------------------------------   
   'baseForm' => array (
      'classname' => 'baseForm',
	  'classpath' => $inf_path. $vendor_dir. 'baseform.inc.php'
   ),
   //-------------------------------------

   'stdLayout' => array (
      'classname' => 'stdLayout',
	  'classpath' => $inf_path. $vendor_dir. 'stdlayout.inc.php'
   ),
   'stdViewHelper' => array (
      'classname' => 'stdViewHelper',
	  'classpath' => $inf_path. $vendor_dir. 'stdviewhelper.inc.php'
   ),
   'debugViewHelper' => array (
      'classname' => 'debugViewHelper',
	  'classpath' => $inf_path. $vendor_dir. 'debugviewhelper.inc.php'
   ),
   'stdValidator' => array (
      'classname' => 'stdValidator',
	  'classpath' => $inf_path. $vendor_dir. 'stdvalidator.inc.php'
   )
   
   
   
   
)




?>