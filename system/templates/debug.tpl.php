<?php

   foreach (array('description','keywords','title','head_scripts','head_styles','content','sidebar') as $var) {
      if (!isset($$var))
	     $$var = '';
   }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  
   <meta name="description" content="<?php echo $description; ?>>" />
   <meta name="keywords" content="<?php echo $keywords;?>" />
   
   <title><?php echo $title; ?></title>
   
   <?php echo $head_scripts; ?>
   <?php echo $head_styles; ?>
</head>
<body>

<div class="container">
   <div class="content">
      <?php echo $content; ?>
   </div>
   <div class="sidebar">
      <?php echo $sidebar; ?>
   </div>
</div>


</body>