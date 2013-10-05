<?php

$config_array = array (

  'DigestManager' => array ( 
      'classname' => 'DigestManager',
	  'classpath' => $inf_path. $classes_dir. 'manager.inc.php'
   ),
   
   'DigestController' => array (
      'classname' => 'DigestController',
	  'classpath' => $inf_path. $controllers_dir. 'dc.inc.php'
   ),
   
   'DigestView' => array (
      'classname' => 'DigestView',
	  'classpath' => $inf_path. $views_dir. 'dv.inc.php'
   ),
   
   'DigestModel' => array (
      'classname' => 'DigestModel',
	  'classpath' => $inf_path. $models_dir. 'dm.inc.php'
   )

)










?>