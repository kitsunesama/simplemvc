<?php

class stdViewHelper implements IInjectable, IConfigurable {

   private $services;
   private $sm;
   private $em;

   private $params = array();
   

   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function inject($serviceName, $service) {
      $this->services[$serviceName] = $service;
   }
   
   public function config() {
   
      $this->sm = $this->services['ServiceManager'];
	  $this->em = $this->sm->getService('EventManager');
	  
   }
   
   //----------------------------------------------------------------------- 
   public function set_config($params) {
   
      $this->params = $params;
   }   
  //-----------------------------------------------------------------------
   private function url ($args) {
   
      $url = '';
      if (isset($this->params['url_builder'])) {
	     $obj = $this->params['url_builder']['object'];
	     $method = $this->params['url_builder']['method'];
		 
		 $url = call_user_func_array(array($obj,$method),array($args));
	  }
	  return $url;
   
   }
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   protected function drawPageEntry($item, $args) {

      $id = $item[$args['id']];
	  unset ( $item[$args['id']] );
	  
      $first_field = array_shift($item);
		
      $s ="\n".'<div class="entrytitle"><a href="'.
         $this->url(array('action'=>'view_action', 'id' => $id )).
		 '">'.$first_field.'</a></div>';
					 
      foreach ($item as $key => $val) {

            $z = $this->em->dispatchEvent('view_page_entry_field_filter',$key, $val,$item);	
			$s .="\n".'<div class="'.$key.'">'.($z ? $z : $val).'</div>';    	  
                                  
      }
 
      $s .= $this->drawButtons( $id, $item);
      return $s;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   protected function drawButtons($id, $item) {
   
      $s = '<div class="entryeditpanel">';   
      $s .= $this->em->dispatchEvent('view_page_entry_before_draw_edit_buttons', $id, $item);
         
      $s .= '<div class="editbutton"><a href="'.
	  
         $this->url ( array('action'=>'edit_action', 'id' => $id) ).
		 
		        '">edit</a></div>';  
                         
      $s .= '<div class="deletebutton"><a href="'.
	  
         $this->url ( array('action'=>'delete_form_action', 'id' => $id) ).
		 
		 '">delete</a></div>';
 
      $s .= $this->em->dispatchEvent('view_page_entry_after_draw_edit_butons', $id, $item);                                                 
      $s .= '</div>';
      $s .= $this->em->dispatchEvent('view_page_entry_after_row', $id, $item);
         
      return $s;
   }

   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function drawPage($args) {
  
	  $this->sm->ss('view_helper_drawpage',
	  'h>called view helper drawPage, args: ',$args);
   
      $s = '<div class="page">';  
      $items = $args['items']['items'];
	  $captions = $args['items']['captions'];
	  
      for ($i=0; $i<count($items); $i++) {
         
                     //--------------- draw captions
                     if ($i==0) {
                            foreach ($items[$i] as $key=>$val) {
                                   if ($key!=$args['id'])
                                      $s .= '<div class="caption" >'.$captions[$key].'</div>';         
                                }
                         }
                         $s .= '<div class="clearfix"></div>';
                 
          $s .= $this->drawPageEntry($items[$i], $args);
      }
      $s .= '</div>';  
	  
	   $this->sm->ss('view_helper_drawpage_result',
	  'h>result of drawPage: ', htmlentities($s));
	  
      return $s;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------  
   public function drawSingleEditPanel($args) {
  
	  $this->sm->ss('view_helper_drawsingleeditpanel',
	  'h>called view helper drawSingleEditPanel, args: ',$args);
   
      $s = '<div class="editpanel">';       
      $s .= $this->em->dispatchEvent('view_single_before_edit_panel', $args['id'], $args['item']);
                 
         
      $s .= '<div class="editbutton"><a href="'.
	  
            $this->url (array('action'=>'edit_action', 'id' => $args['id'])).
			
		    '">edit</a></div>';
                                                                 
      $s .= '<div class="deletebutton"><a href="'.
	  
         $this->url (array('action'=>'delete_form_action', 'id' => $args['id'])).
		 
		 '">delete</a></div>';
                                                                 
      $s .= '<div class="backtolistbutton"><a href="'.
	  
         $this->url (array( 'action'=>'page_action', 'page' => $args['page'])).
		 
		 '">back to list</a></div>';                
           
		  
      $s .= $this->em->dispatchEvent('view_single_after_edit_panel', $args['id'], $args['item']);
      $s .= '</div>';
      
      $this->sm->ss('view_helper_drawsingleeditpanel_result',
	  'h>result of drawSingleEditPanel: ', htmlentities($s));
	  
       return $s;
   }   
   
   //-----------------------------------------------------------------------
   //----------------------------------------------------------------------- 
   public function drawEditPanel() {
   
      
	  
	  $this->sm->ss('view_helper_draweditpanel',
	  'h>called view helper drawEditPanel');
	  
      $s = '<div class="editpanel">';       
                     
      $s .= '<div class="addbutton"><a href="'.
	     $this->url (array('action'=>'add_action')).
		 '">add</a></div>';
                      
      $s .= '</div>';
	  
	  $this->sm->ss('view_helper_draweditpanel_result',
	  'h>result of drawEditPanel: ', htmlentities($s));
         
      return $s;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function drawNavigation($args) {
   
 
	  $this->sm->ss('view_helper_drawnavigation',
	  'h>called view helper drawNavigation, args: ',$args);
	  
      $s ='<div class="navigation"><ul>';
         
      if ($args['num_pages']>1)
	  
         for ($i=0; $i < $args['num_pages']; $i++) {
             
            $s .='<li>';            
            if ($i== $args['page']) {          
               $s .= ($i+1);                      
            } else {                
               $s .= '<a href="'. 
			      $this->url (array('action'=>'page_action', 'page' => $i)).
				  '">'.($i+1).'</a>';            
            }
                 
            $s .= '</li>';
         }
		 
       $s .= '</ul></div>';
	   
	   $this->sm->ss('view_helper_drawnavigation_result',
	  'h>result of drawNavigation: ', htmlentities($s));
                 
       return $s;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------   
   public function drawForm($form) {
        
	  $s = $form->draw();
      return $s;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function drawMessage($message) {
   
      $s = '<div class="message">'.$message.'</div>';
	  return $s;
   }
   
   //-----------------------------------------------------------------------
   //-----------------------------------------------------------------------
   public function drawSingle($item) {
   
       $this->sm->ss('view_helper_drawsingle',
	  'h>called view helper drawSingle, item: ', $item);
	  
       $s = '';         
       $captions = $item['captions'];	   
	   
       foreach ($item['item'] as $key=>$value) {   
         $s .= '<div class="caption">'.$captions[$key].'</div>';            
	     $s .= '<div class="'.$key.'">'.$value.'</div>';	             					 
      }        
				  
      $this->sm->ss('view_helper_drawsingle_result',
	  'h>result of drawSingle: ', htmlentities($s));  
	  
      return $s;
   }
}













?>