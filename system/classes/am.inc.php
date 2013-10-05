<?php

abstract class AbstractModel implements IInjectable {
   
   protected $services = array();
   protected $sm;
   protected $db;
   
   protected $serverside_replacements;
   protected $serverside_validator;
   public $clientside_replacements;
   public $clientside_validator;
   
   protected $dbadapter_strategy;
   
   
   private $mapped_data = array();
   
   private $config = array();
   
   private $recs_per_page = false;
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function inject($serviceName, $service) {
      $this->services[$serviceName] = $service;
   }
   
   //-----------------------------------------------------------------------  
   public function setDbAdapterStrategy($strategy) {
   
      $this->dbadapter_strategy = $strategy;
   }
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function setTable($table) {
      $this->config['table'] = $table;
   }
   public function getTable() {
      return $this->config['table'];
   }
   public function setIdField($id_field) {
      $this->config['id_field'] = $id_field;
   }
   public function getIdField() {
      return $this->config['id_field'];
   }
   public function setFilterString($filter_string) {
      $this->config['filter_string'] = $filter_string;
   }
   public function getFilterString() {
      return isset($this->config['filter_string']) ? $this->config['filter_string'] : '' ;
   }
   public function setOrderString($order_string) {
      $this->config['order_string'] = $order_string;
   }
   public function getOrderString() {
      return isset($this->config['order_string']) ? $this->config['order_string'] : '';
   }
   
   //-----------------------------------------------------------------------
   protected function addFields($fields) {
      $this->config['fields'] = $fields;
   }
   public function getFields() {
      return $this->config['fields'];
   }
   //-----------------------------------------------------------------------
   private function fields_in_list($a) {
      return (isset($a['show_in_list'])) && ($a['show_in_list']==true);
   }
   
   public function getListFields() {
      
	  $fields = $this->getFields();
	  $fields = array_filter($fields, array($this, 'fields_in_list'));
	  return $fields;
   }

   
   //-----------------------------------------------------------------------
   abstract protected function configDbMapping();
  
   protected function configValidators() {
   }
   
   protected function getAutoFields() {
      return array();
   }
   //----------------------------------------------------------------------- 
   public function init() {
    
      $this->sm = $this->services['ServiceManager'];
	  $this->sm->ss('init_abstract_model', 'h>start configuring abstract model');
	  
      $this->db = $this->dbadapter_strategy->getDbAdapter(); 
   
      $this->configDbMapping();
	  
	 
	  
	  $params = array(
		    'table' => $this->getTable(),
			'id_field' => $this->getIdField(),
			'filter_string' => $this->getFilterString(),
			'order_string' => $this->getOrderString(),
			'fields' => $this->getFields()
		 );
	  $this->sm->ss('init_abstract_model', 'h>abstract model params', 
	  array_diff_key($params, array('fields'=>true)));

	  $this->db->setupModelParams($params);
	                  
	  $this->sm->ss('init_abstract_model', 'h>creating 4 standart validators');
	  
	  $this->serverside_replacements = $this->sm->getService('stdValidator');
	  $this->serverside_validator = $this->sm->getService('stdValidator');
	  $this->clientside_replacements = $this->sm->getService('stdValidator');
	  $this->clientside_validator = $this->sm->getService('stdValidator');
	  
	  $this->sm->ss('init_abstract_model', 'h>configuring validators');
	  
	  $this->configValidators();
   }
   
   //-----------------------------------------------------------------------
   //----------------------------------------------------------------------- 
   public function setRecsPerPage($nrecs) {
   
      //$this->sm->ss('absrtact_model_recsperpage','h>Abstract model: records per page: '.$nrecs);
      $this->recs_per_page = $nrecs;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function map_data($data) {
   
      $this->sm->ss('absrtact_model_mapdata',
	  'h>Abstract model: map data: ', $data);
   
      $this->mapped_data = 
	     array_intersect_key(
		    array_merge($data, $this->getAutoFields()), $this->getFields());
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function get_mapped_data() {
   
      $this->sm->ss('absrtact_model_mappeddata',
	  'h>Abstract model: get mapped data: ', $this->mapped_data);
   
      return $this->mapped_data;
   }
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------  
   public function getSingle($record_id) {
      
	  $this->sm->ss('absrtact_model_getsingle',
	  'h>Abstract model: get single record: '. $record_id);
	  
	  $item = $this->db->getRecordById($record_id); 
	  $item = array_intersect_key($item, $this->getFields());
	    
	  $captions = array();
	  $fields = $this->getFields();
	  foreach ($fields as $key=>$field) {
	     $captions[$key] = isset($field['caption']) ? $field['caption'] : '' ;
	  }
	  
	  return array('captions'=>$captions, 'item'=>$item);
	  
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function getPage($page_id = 0) {
   
       $this->sm->ss('absrtact_model_getpage',
	   'h>Abstract model: get page: '. $page_id);
   
       $items = $this->db->getNRecords($page_id * $this->recs_per_page, $this->recs_per_page);
	   
	   foreach ($items as $key=>$item) {
	      $items[$key] = 
		     array_intersect_key($item, 
		        array_merge($this->getListFields(),array($this->getIdField()=>true))
				);
	   }
	   
	  $captions = array();
	  $fields = $this->getFields();
	  foreach ($fields as $key=>$field) {
	     $captions[$key] = isset($field['caption']) ? $field['caption'] : '' ;
	  }
	   
	   return array('captions'=>$captions, 'items'=>$items);
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function getAll() {
   
      $items = $this->db->getNRecords( 0, 1000);
	  
	  return $items;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function getNumPages() {
   
      $this->sm->ss('absrtact_model_getnumpages',
	  'h>Abstract model: get number of pages ');
	  
      $nrecs = $this->db->getNumRecs();    
	  $num_pages = ceil($nrecs/$this->recs_per_page);
	  
      return $num_pages;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function insertItem() {
   
      $this->sm->ss('abstract_model_insert','h>Abstract model: insert item ');
   
      $data = $this->get_mapped_data();
	  $data = $this->serverside_replacements->replace($data);
	  
	   $this->sm->ss('abstract_model_replace',
	   'h>Abstract model: performed replacements: ', $data);
	  
	  if (!($validation_error = $this->serverside_validator->validate($data))) {
	  	     
		 return $this->db->insertRecord($data);
	  } else {
	     
		  $this->sm->ss('abstract_model_validation_error',
	     'e>Abstract model: validation errors: ', $validation_error);
	  
	     return $validation_error;
	  }
   
      
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function updateItem($record_id) {
   
      $this->sm->ss('absrtact_model_update','h>Abstract model: update item: '.$record_id);
	  
      $data = $this->get_mapped_data();
	  $data = $this->serverside_replacements->replace($data);
	  
	  $this->sm->ss('abstract_model_replace',
	  'h>Abstract model: performed replacements: ', $data);
	  
	  if (!($vaildation_error = $this->serverside_validator->validate($data))) {
	  	     
		 return $this->db->updateRecord($record_id, $data);
	  } else { 
	  
	  	 $this->sm->ss('abstract_model_validation_error',
	     'e>Abstract model: validation errors: ', $validation_error);
	  
	     return $validation_error;
	  }
   
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function deleteItem($record_id) {
   
      $this->sm->ss('absrtact_model_delete','h>Abstract model: delete item: '.$record_id);
	  
      $rez = $this->db->deleteRecord($record_id);
	  return $rez;
   }
   
   
}










?>