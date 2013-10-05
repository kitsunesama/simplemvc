<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
   foreach (array('description','keywords','title','head_scripts',
   'head_styles','message_master','content_master','message_detail',
   'content_detail', 'sidebar') as $var) {
      if (!isset(${$var}))
	     $$var = '';
   }

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  
   <meta name="description" content="<?php echo $description; ?>" />
   <meta name="keywords" content="<?php echo $keywords;?>" />
   
   <title><?php echo $title; ?></title>
   
   
   <?php echo $head_scripts; ?>
   <?php echo $head_styles; ?>
   <script type="text/javascript" src="assets/jquery.min.js"></script>
   <link rel="stylesheet" type="text/css" media="all" href="assets/main.css" />
</head>
<body>

<div class="container" id="dkdigest_container">

   <div class="message">
      <?php echo $message_master; ?>
   </div>
   <div class="content">
      <?php echo $content_master; ?>
   </div>

   <div class="message">
      <?php echo $message_detail; ?>
   </div>
   <div class="content">
      <?php echo $content_detail; ?>
   </div>

   <div class="sidebar">
      <?php echo $sidebar; ?>
   </div>
</div>


</body>