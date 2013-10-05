<?php

$config_array = array (

   //-------------------------------------
   'ApplicationManager' => array (

      'classname' => 'ApplicationManager',
	  'classpath' => $inf_path. $classes_dir. 'appmanager.inc.php',
          'singleton' => true
   ),
   //-------------------------------------
   'App_dbAdapter_strategy' => array (

      'classname' => 'AppDbAdapterStrategy',
	  'classpath' => $inf_path. $classes_dir. 'dbadapter_strategy.inc.php',
          'singleton' => true
   ),
   //-------------------------------------
   'dbAdapter_mysqli' => array (
      'classname' => 'mysqli_dbAdapter',
	  'classpath' => $inf_path. $vendor_dir. 'mysqli'. $ds. 'dbadapter.inc.php'
   ),
   
   'dbConn_mysqli' => array (
      'classname' => 'mysqli_dbconn',
	  'classpath' => $inf_path. $vendor_dir. 'mysqli'. $ds. 'dbconn.inc.php',
          'singleton' => true
   ),
   //-------------------------------------
   'wp_adapter' => array (
      'classname' => 'WpAdapter',
	  'classpath' => $inf_path. $vendor_dir. 'wp'. $ds. 'wpadapter.inc.php',
          'singleton' => true
   ),
   //-------------------------------------
   'dbAdapter_wp' => array (
      'classname' => 'WP_dbAdapter',
	  'classpath' => $inf_path. $vendor_dir. 'wp'. $ds. 'dbadapter.inc.php'
   ),

   //-------------------------------------
   'BaseForm' => array (
      'classname' => 'SimpleForm',
	  'classpath' => $inf_path. $classes_dir. 'form.inc.php'
   ),
   
   //-------------------------------------
   'FormBuilder' => array (
      'classname' => 'FormBuilder',
	  'classpath' => $inf_path. $classes_dir. 'fbuilder.inc.php',
          'singleton' => true
   ),
    
   'JsFormDecorator' => array (
      'classname' => 'JsFormDecorator',
	  'classpath' => $inf_path. $classes_dir. 'jsfdecorator.inc.php',
          'singleton' => true
   ),
   
   'FillFormDecorator' => array (
      'classname' => 'FillFormDecorator',	  
	  'classpath' => $inf_path. $classes_dir. 'fillfdecorator.inc.php',
          'singleton' => true
	 
   )
 
)













?>