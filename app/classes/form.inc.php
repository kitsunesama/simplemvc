<?php

//----------------------------------------------
//                  Form
//----------------------------------------------
class SimpleForm {
       
   		public $id;   
		
        public $action_route;
		public $cancel_route;

        public $fieldsets = array();
        public $hidden_fields = array();
        public $buttons = array();
		
		public $scripts = array();
		public $js_rules = array();
		public $field_hints = array();
       
     //-------------------------   check if empty
       
        public function nzz($var, $default) {
           return (isset($var)) ? $var : $default;
        }
        //-------------------------   constructor
       
        public function __construct() {
                
           $this->id = 'form_'.rand(10,5000);
        }    
		//-------------------------------------------
		public function get_field_hints() {
		   return $this->field_hints;
		}
		//-------------------------------------------
		public function get_js_rules() {
		   return $this->js_rules;
		}
		//-------------------------- set field hint
		
		public function setFieldHint($field_name, $value) {
		
		   $this->field_hints[$field_name] = $value;
		}

		//-------------------------- set js rule
		
		public function set_js_rule($field_name, $value) {
		
		   $this->js_rules[$field_name] = $value;
		}		
		
		//-------------------------- set script
		
		public function set_script($script_name, $value) {
		
		   $this->scripts[$script_name] = $value;
		}			
        //-------------------------- set form action route
       
        public function set_action_route ($action_route) {
       
           $this->action_route = $action_route;
        }
		//-------------------------- set form cancel route
		public function set_cancel_route ($cancel_route) { 
           $this->cancel_route = $cancel_route;
        }
 
        //--------------------------   add fieldset
       
        public function addFieldset ($legend) {
           
           $a = new stdFieldset($legend);
           $this->fieldsets[] = & $a;
           return $a;
        }      
        //--------------------------   add hidden field
       
        public function addHidden ($name, $value) {
       
           $this->hidden_fields[] = new stdInput ('HIDDEN', $name, $value);
        }      
        //--------------------------   add button
       
        public function addButton ($type, $name, $value, $onclick='') {
           
           if (is_array($onclick)) {       
              $this->scripts[$name] = array ('handler'=>$onclick[0], 'script'=>$onclick[1]);  
           }
           
           $a = new stdInput ($type, $name, $value);
           
           if (is_array($onclick))
              $a->addHandler(array('onclick', $onclick[0]));
                 
           $this->buttons[] = & $a;
        }      
        //---------------------------   draw header
       
        private function drawHeader() {
           
           $s = '';
           foreach ($this->scripts as $script) {
           
              $s .= $script['script'];
           }
           
           
           $subm = (isset($this->scripts['onsubmit'])) ?
                             'onsubmit="'.$this->scripts['onsubmit']['handler'].'"':'';
                                                 
           $s .= '<form '.$subm.' id="'.$this->id.'" action="'.$this->action_route.'" method="post" '.
             ' enctype="application/x-www-form-urlencoded" >'; 
 
       return $s;                        
        }      
        //---------------------------   draw footer
       
        private function drawFooter() {
           
           return '</form>';
        }      
        //---------------------------   draw form
        public function draw() {
       
           $s = '';
           $s .= $this->drawHeader();
           
           $func = create_function('$a',' echo $a->draw();');
           
           ob_start();     
           array_walk($this->fieldsets, $func);
           array_walk($this->hidden_fields, $func);
           array_walk($this->buttons, $func);      
           $s .= ob_get_contents();
           ob_end_clean();
           
           $s .= $this->drawFooter();
           return $s;
        }    
}


//----------------------------------------------
//               form fieldset
//----------------------------------------------
class stdFieldset {
   
   public $fields = array();
   private $legend;
   
   //------------------------------------ constructor
   public function __construct($legend) {
     
          $this->legend = $legend;
   }
   
   //------------------------------      
   public function hasField($name) {
   
      for ($i=0; $i<count($this->fields); $i++) {
             if ($this->fields[$i]->_name == $name)
                    return true;
          }
          return false;
   }
   
     //------------------------------      
   public function getField($name) {
   
      for ($i=0; $i<count($this->fields); $i++) {
             if ($this->fields[$i]->_name == $name)
                    return $this->fields[$i];
          }
          return false;
   }
   
   //------------------------------   add field
   
   public function addField($type, $name, $value, $label, $group_caption='') {
     
          $a = new stdInput ($type, $name, $value, $label, $group_caption);
          $this->fields[] = & $a;
         
   }
   //------------------------------   draw field
   
   public function draw() {
     
          $s = "<fieldset><legend>{$this->legend}</legend>";
         
          for ($i=0; $i<count($this->fields); $i++)
             $s .= $this->fields[$i]->draw();
                 
          $s .= '</fieldset>';
          return $s;
   }
   
}

//----------------------------------------------
//               form input
//----------------------------------------------
class stdInput {
   
   private $_type;
   public $_name;
   private $_value;
   private $_values = array();
   private $_label = '';
   private $_labels = array();
   private $_group_caption;
   
   private $_handlers = array();
   //--------------------------------   constructor
   
   public function __construct($type, $name, $value, $label='', $group_caption='') {
     
          $this->_type = $type;
          $this->_name = $name;
          $this->_group_caption = $group_caption;
         
          //------------------------- values
          $this->setValue($value);
         
          //------------------------- labels
          if (is_array($label)) {
             for ($i=0; $i<count($label); $i++) {
                    $this->_labels[] = $label[$i];
                 }
          } else {
             $this->_label = $label;
          }
   }  
   //------------------------------------------- set field values
   
   public function setValue($value) {
   
          //-------------------- checkboxes and radiogroups
          if (is_array($value)) {
         
             $this->_values = array();
                 
                 for ($i=0; $i<count($value); $i++) {
                   
                        $el = array();
                        if (substr($value[$i], strlen($value[$i])-1,1)=='*') {
                       
                           $el['checked']=true;
                           $el['value'] = substr($value[$i],0,strlen($value[$i])-1);
                        } else {
                       
                           $el['checked']=false;
                           $el['value'] = $value[$i];
                        }
                        $this->_values[] = $el;                
                 }               
          } //------- single components ( text / password / button / submit /reset )     
          else {
             $this->_value = $value;
          }
   }
   //-------------------------------------------   add handler
   public function addHandler ($handler) {
     
          $this->_handlers[] = array ('event'=>$handler[0], 'handler'=>$handler[1]);
   }
   //------------------------------   draw input
   public function draw() {
   
      $s = '';
          $handlers_str = '';
         
          for ($i=0; $i<count($this->_handlers); $i++) {
         
             $handlers_str .= $this->_handlers[$i]['event'].'="'.$this->_handlers[$i]['handler'].'" ';
          }
         
          switch (strtolower($this->_type)) {
             //--------------------------
             case 'text': case 'password': case 'submit':
                 case 'reset': case 'button': case 'file': case 'hidden':
                 
                    if ($this->_label) {
                           $s .= "<label for=\"{$this->_name}\">{$this->_label}</label>";                        
                        }
                       
                        $s .= "<input $handlers_str type=\"{$this->_type}\" ".
                              "name=\"{$this->_name}\" value=\"{$this->_value}\" />";
                        break;
                 //--------------------------
                 case 'textarea':
                    if ($this->_label)
                           $s .= "<p>{$this->_label}</p>";
                       
                        $s .= "<textarea $handlers_str rows=\"10\" cols=\"90\" name=\"{$this->_name}\">{$this->_value}</textarea><br/>";
                        break;
                       
         //--------------------------
                 case 'radio': case 'checkbox':
                 
                    $s .= "<label for=\"{$this->_name}\">{$this->_group_caption}</label>";
                       
                    for ($i=0; $i<count($this->_values); $i++) {
                       
                           //$checked = ($this->_values[$i]['checked']) ? 'checked' : '';
                           $checked = ($this->_values[$i]['value']==$this->_value) ? 'checked' : '';
                           
                           $s .= "<p><input $handlers_str type=\"{$this->_type}\" ".
                                 " name=\"{$this->_name}\" value=\"{$this->_values[$i]['value']}\" $checked />";
                                         
                           $s .= $this->_labels[$i].'</p>';      
                        }
                    break;
                       
                 //--------------------------
                 case 'select':
                 
                    $s .= "<label for=\"{$this->_name}\">{$this->_group_caption}</label>";
                       
                    $s .= "<select $handlers_str name=\"{$this->_name}\">";
                        for ($i=0; $i<count($this->_values); $i++) {
                           
                           $selected = ($this->_values[$i]['value']==$this->_value) ? 'selected' : '';
                           $s .= "<option value=\"{$this->_values[$i]['value']}\" $selected >";
                           $s .= $this->_labels[$i].'</option>';
                        }
                        $s .= '</select><br/>';
                        break;
          }
         
          return $s;
   }
}
 


?>