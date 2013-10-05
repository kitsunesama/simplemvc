<?php

class EasyView extends AbstractView {

   protected $layout_blocks = 
   array (
      'content_block' => 'content',
	  'message_block' => 'message'
   );
   
   protected $sm;
   protected $router;
   //----------------------------------------------------------------
   
   public function setBlockName($block, $name) {
   
      $this->layout_blocks[$block] = $name;
   }
   //----------------------------------------------------------------
   public function url($arguments) {
   	  
	  $actions = array (
	     'page_action' => 'index',
		 'add_action' => 'add',
		 'view_action' => 'view',
		 'edit_action' => 'edit',
		 'delete_form_action' => 'delete_form',
         'delete_action'=>'delete'		 
	  );
	  
	  $stdargs = array (
	     'id' => $this->get('dbkey'),
		 'page' => $this->get('pagekey'),
	  );
	  
	  foreach ($stdargs as $key=>$newkey) {  
	     if (array_key_exists($key, $arguments)) {	 
		    $arguments[$newkey] = $arguments[$key];
			unset ($arguments[$key]);
		 }
	  }
	  
	  $action = $actions[$arguments['action']];
	  unset ($arguments['action']);
	  
	  $route = $this->router->build_route(
	     $this->get('module'),
		 $this->get('route_build_template'),
		 array($action),
		 $arguments);
		 
	  return $route; 
   }
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   protected function before_render($action) {
   
      $this->sm = $this->services['ServiceManager'];
	  $this->sm->ss('easy_view_before_render','h>EasyView: before render procedure');
	  
      $this->router = $this->sm->getService('Router');
	  
	  
   
      $this->helper->set_config(
	     array (	
			'url_builder' => array (
			   'object' => $this,
			   'method' => 'url'
			)
		 )
	  );
   }   
  
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   protected function show_page() {
   
      $a = $this->helper->drawPage(
	     array(
		    'items'=>$this->get('items'),
	        'id' => $this->get('dbkey')
		   )
		);  
			
	  $b = $this->helper->drawEditPanel();
	  $c = $this->helper->drawNavigation(
	     array(
		    'num_pages' => $this->get('numpages'),
			'page' => $this->get('page')
		 ));
	  
	  return $a.$b.$c;
   }

   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function view_index() {
   
      $a = $this->show_page();
	  $this->layout->setBlock( $this->layout_blocks['content_block'], $a);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function view_view() {
   
      $a = $this->helper->drawSingle($this->get('item'));
	  
	  $b = $this->helper->drawSingleEditPanel(
	     array (
		    'item' => $this->get('item'),
			'id' => $this->get('item_id'),			
			'page' => $this->get('page')
		 )
	  );
	  
	  $this->layout->setBlock( $this->layout_blocks['content_block'], $a.$b);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function view_add() {
   
      $a = $this->helper->drawForm($this->get('form'));
	  $this->layout->setBlock( $this->layout_blocks['content_block'], $a);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function view_insert() {
   
     $a = $this->helper->drawMessage($this->get('message'));
     $b = $this->show_page();
	 
	 $this->layout->setBlock( $this->layout_blocks['message_block'], $a);
	 $this->layout->setBlock( $this->layout_blocks['content_block'], $b);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function view_edit() {
   
      $a = $this->helper->drawForm($this->get('form'));
	  $this->layout->setBlock( $this->layout_blocks['content_block'], $a);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function view_update() {
   
      $a = $this->helper->drawMessage($this->get('message'));
      $b = $this->show_page();
	  $this->layout->setBlock( $this->layout_blocks['message_block'], $a);
	  $this->layout->setBlock( $this->layout_blocks['content_block'], $b);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function view_delete_form() {
   
      $a = $this->helper->drawMessage ($this->get('message')).'<br/>'.
	     '<a href="'.
	     $this->url(array('action'=>'delete_action','id'=>$this->get('item_id'))).
		 '">delete</a><br/><a href="'.
		 $this->url(array('action'=>'page_action')).'">cancel</a><br/>';
		 
	  $this->layout->setBlock( $this->layout_blocks['message_block'], $a);	 
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function view_delete() {
   
     $a = $this->helper->drawMessage($this->get('message'));
     $b = $this->show_page();
	 
	 $this->layout->setBlock( $this->layout_blocks['message_block'], $a);
	 $this->layout->setBlock( $this->layout_blocks['content_block'], $b);
   }
   
}










?>