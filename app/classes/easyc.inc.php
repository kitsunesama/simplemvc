<?php

abstract class EasyController extends AbstractController {

   protected $sm;
   protected $em;
   protected $router;
   protected $model;
   protected $dbkey = 'id';
   protected $pagekey = 'page';
   
   abstract protected function getModelName();
   abstract protected function getPageKey();
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function init() {

      $this->sm = $this->services['ServiceManager'];	  
	  $this->sm->ss('easy_controller_init', 
	  'h>start init in Easycontroller, controller: '.$this->module_name.' / '.$this->name);
	  
	  $this->em = $this->sm->getService('EventManager');
	  $this->router = $this->sm->getService('Router');
	  
      $modelName = 	$this->getModelName();
      $module = $this->getModule();	  
	  $this->sm->ss('retrieving_model', 
	  'h>EasyController attemptong to retrieve model: '.$module.' / '.$modelName);
	  
	  $this->model = $this->sm->getModel($modelName, $module );
	  
	  $this->dbkey = $this->model->getIdField();
	  $this->pagekey = $this->getPageKey();//'digest_page';
	  
	  $viewName = str_replace('Controller','View', $this->name);
	  
	  $this->sm->ss('retrieving_view', 
	  'h>EasyController retrieving standart View : '.$module.' / '.$viewName);
	  
	  $this->setView($this->sm->getView( $viewName , $module ));

   }
   //----------------------------------------------------------------
   public function get_dbkey() {
   
      return $this->dbkey;
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function render() {
   
      $this->sm->ss('easy_controller_render', 'h>start rendering easy controller');
   
      $this->view->set('module', $this->module_name);
	  $this->view->set('route_build_template', $this->route_build_template);
	  $this->view->set('dbkey', $this->dbkey);
	  $this->view->set('pagekey', $this->pagekey);
	  
	  $page = isset($this->arguments[$this->pagekey]) ? $this->arguments[$this->pagekey] : 0;
	  $this->view->set('page',$page);
	   
      parent::render();
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   protected function set_page_info($args) {
   
      $page = isset($args[$this->pagekey]) ? $args[$this->pagekey] : 0;    
	  $els = $this->model->getPage($page);
	  
	  $this->view->set('items', $els); 
	  $this->view->set('numpages', $this->model->getNumPages());
   }

   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function action_index($args) {
   

	  $this->set_page_info($args);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function action_view($args) {
      
	  
	  $el = $this->model->getSingle($args[$this->dbkey]);
	  $this->view->set('item', $el);
	  $this->view->set('item_id', $args[$this->dbkey]);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------   
   public function action_add() {
   
      $form = $this->sm->getService('BaseForm','application');
	  
	  $b = $this->sm->getService('FormBuilder','application');
	  $js = $this->sm->getService('JsFormDecorator','application');
	  	  
	  $js->wrap($b);
	  
	  $form->set_cancel_route(
	     $this->router->build_route(
		    $this->getModule(),
			$this->route_build_template,
			array('index'), 
			array()));		  
  
	  $form = $js->build ($form, $this->model);
	  
	  $form->set_action_route(
	     $this->router->build_route(
		    $this->getModule(),
			$this->route_build_template,
			array('insert'), 
			array()));
			

	  
	  $this->view->set('form', $form);
      
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function action_insert($args) {
   
       $this->model->map_data($this->getPostData());
	   
       $inf = $this->model->insertItem();
         
       if (is_array($inf)) {
	   
          $this->view->set('message', $inf['message']);
          $this->view->set('newrecord_id', $inf['rid']);
		  
       } else
	   
          $this->view->set('message', $inf);
			 
      $this->set_page_info($args);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function action_edit($args) {
   
      $form = $this->sm->getService('BaseForm','application');
	  
	  $b = $this->sm->getService('FormBuilder','application');
	  $js = $this->sm->getService('JsFormDecorator','application');
	  $fill = $this->sm->getService('FillFormDecorator','application');
	  
	  $js->wrap($b);
	  $fill->wrap($js);
	  
	  $this->model->map_data($this->model->getSingle($args[$this->dbkey]));
	  
	  $form->set_cancel_route(
	     $this->router->build_route(
		    $this->getModule(),
			$this->route_build_template,
			array('index'), 
			array()));	  
			
	  $form = $fill->build ($form, $this->model);
	  
	  $form->set_action_route(
	  
	      $this->router->build_route(
		    $this->getModule(),
			$this->route_build_template,
			array('update'), 
			array($this->dbkey => $args[$this->dbkey]))
			
	      );
		  
	
	  
	  $this->view->set('form', $form);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function action_update($args) {
   
      $this->model->map_data($this->getPostData());
   
      $this->view->set('message',
	     $this->model->updateItem($args[$this->dbkey]));
   
      $this->set_page_info($args);   
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function action_delete_form($args) {
   
      $this->view->set('message', 'really delete record?');
	  $this->view->set('item_id', $args[$this->dbkey]);
   }
   
   //----------------------------------------------------------------
   //----------------------------------------------------------------
   public function action_delete($args) {
   
      $this->view->set('message',
	  $this->model->deleteItem($args[$this->dbkey]));
   
      $this->set_page_info($args);   
	  
   }
}

















?>