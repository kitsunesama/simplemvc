<?php

//----------------------------------------------
//                  DBAdapter
//----------------------------------------------
class mysqli_dbAdapter implements IInjectable {
   
   private $_db;
   private $sm;
   private $_model_params = array();
   
   private $services = array();
   
   //------------------------------------------------
   public function inject($serviceName, $service) {
   
      $this->services[$serviceName] = $service;	  
   }
   //------------------------------------------------
   public function setup($connection) {
   
      $this->sm = $this->services['ServiceManager'];
      $this->_db = $connection->get();
   }
   

   //------------------------------
   public function setupModelParams($params) {
   
      $this->_model_params = $params;
   }
   
   //------------------------------ check errors
   
   private function checkResult($result, $err, $sql='') {
   
      if ($result===false) 
	     throw new Exception ('db error '.$err.'<br/>sql: '.$sql, 30044);
   }
   
   //------------------------------ get full table name
   
   public function getTableName($tabname) {
      return $tabname;
   }
   
   //------------------------------ get single record from model table
   
   public function getRecordById($id) {
	
   	   
	   $sql =  'select * from '.$this->_model_params['table'].
	   ' where '.$this->_model_params['id_field'].'='. $this->_db->real_escape_string($id);
	   
	   $this->sm->ss('db_adapter_getrecordbyid','g>DB Adapter: performing sql:',$sql);
                      					  
	   $result = $this->_db->query($sql);
	   $this->checkResult($result, $this->_db->error, $sql);
	   
	   $row = $result->fetch_array(MYSQLI_ASSOC);
	   
	   $result->close();
	  
	   return $row;
	}

   //------------------------------ get N records from model table
   
   public function getNRecords($start, $num) {
      
	  
	  $sql =  'select * from '.$this->_model_params['table'].
	           ($this->_model_params['filter_string'] ? ' where ':'').
			      $this->_model_params['filter_string'].
	          ' order by '.$this->_model_params['order_string'];
			  
	  $this->sm->ss('db_adapter_getnrecords','g>DB Adapter: performing sql:',$sql);
			  	
	  $result = $this->_db->query($sql);
	  
	  $this->checkResult($result, $this->_db->error, $sql);
		 
	  $result->data_seek($start);
	  
	  $i=0;
	  $ret = array();
	  while (($row = $result->fetch_assoc()) && ($i<$num)) {
	  
	     $ret[] = $row;
		 $i++;
	  }
	  $result->close();
	  //$this->_w($ret, __METHOD__);
	  return $ret;
   }

   //------------------------------ get number of records in model table
   
  public function getNumRecs() {
   
      $sql = 'select count(*) from '.$this->_model_params['table'];
	  
	  $this->sm->ss('db_adapter_getnumrecs','g>DB Adapter: performing sql:',$sql);
	  
      $result = $this->_db->query($sql);
	  
	  $this->checkResult($result, $this->_db->error, $sql);
	  
	  $inf = $result->fetch_row();
	  $result->close();
	  
	  return $inf[0];
	  
   }
   
   //------------------------------ inserting and updating records
   
   private function processRecord($type='update',$record_id,$data) {
      
	  $field_names = array();
	  $field_types = array();
	  $field_values = array();	  
	  $func4 = create_function('$a','return $a."=?";');
	    
	  foreach ($data as $key=>$value) {	     
		 $field_names[] = $key;
		 $field_values[] = & $data[$key];	 	
		 $field_types[] = $this->_model_params['fields'][$key]['type'];
	  }
	  //--------------------------
	  
	  if ($type=='insert') {  
	     $field_names_s = implode(',',$field_names);
	     $insert_param_string = implode(',' , array_fill(0, count($field_names),'?'));	 
	  } else {     
	     $field_names_s = implode(',' , array_map($func4, $field_names));
	  }
	  //--------------------------
	  $field_types_s = implode('',$field_types);
	  
	  array_unshift($field_values, $field_types_s);
	  
      //--------------------------- 
	  if ($type=='insert')
	     $sql = 'insert into '.$this->_model_params['table'].'('.$field_names_s.') values ('.
		    $insert_param_string.')';
	  else
         $sql = 'update '.$this->_model_params['table'].' set '.$field_names_s.
		  ' where '.$this->_model_params['id_field'].'='.$this->_db->real_escape_string($record_id);
	 
	  //--------------------------- 
	  
	  $stmt = $this->_db->prepare ($sql);
	  
	  
	  call_user_func_array(array($stmt,'bind_param'), $field_values);
	  
	  $rez = $stmt->execute();
	  $this->checkResult($rez, $stmt->error, $sql);
	  
	  $rid = ($type=='insert') ? $stmt->insert_id : $stmt->affected_rows ;
	  $stmt->close();
	  
	  return($rid);
  
   }
   //-------------------------------------- insert record   
   public function insertRecord($data) {
   
      $this->sm->ss('db_adapter_insertrecord',
	  'g>DB Adapter: inserting record, data:',$data);
   
      $rid = $this->processRecord ('insert', 0, $data);	  
	  return array('message'=>'record successfully inserted. record id: '.$rid, 'rid'=>$rid);
   }
   
   //-------------------------------------- update record
   
   public function updateRecord($record_id, $data) {
   
      $this->sm->ss('db_adapter_updaterecord',
	  'g>DB Adapter: updating record ['.$record_id.'], data:',$data);
   
      $affected_rows = $this->processRecord('update', $record_id, $data);
	  return 'record successfully updated. affected rows: '.$affected_rows;
   }
   
   //--------------------------------------- delete record
   public function deleteRecord($record_id) {
   
      $this->sm->ss('db_adapter_deleterecord',
	  'g>DB Adapter: deleting record ['.$record_id.'] ');
   
      $sql = 'delete from '.$this->_model_params['table'].' where '.$this->_model_params['id_field'].'=?';
	  
	  $this->sm->ss('db_adapter_delete','g>DB Adapter: performing sql:',$sql);
	  
      $stmt = $this->_db->prepare($sql);
	  
	  $stmt->bind_param('i', $record_id);
	  
	  $rez = $stmt->execute();
	  $this->checkResult($rez, $stmt->error, $sql);
	  
	  $affected_rows = $stmt->affected_rows;
	  $stmt->close();
	  
	  return ('record successfully deleted. affected rows: '.$affected_rows);
   }
}

?>