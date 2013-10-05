<?php

class FormBuilder extends AbstractFormBuilder {

   protected function internal_build($form, $model) {
      
      $fset = $form->addFieldset('');
                 
      foreach ($model->getFields() as $field_name => $field_params) {
                     
         if ($field_params['input_type']!='none') {
            
            if (isset($field_params['hint'])) {
               $form->setFieldHint( $field_name, $field_params['hint']);
            }
                      
            if ($field_params['input_type']=='select') {
			
               $fset->addField('SELECT',
			   $field_name,
			   $field_params['select_values'], 
			   $field_params['select_captions'],
			   $field_params['caption']);
			   
            } else
			
               if ($field_params['input_type']=='hidden')
                  $form->addHidden($field_name,'');
               else
                  if ($field_params['input_type']=='textarea')                                       
                     $fset->addField('TEXTAREA', $field_name,'', $field_params['caption']);
                  else
                     $fset->addField('TEXT', $field_name, '', $field_params['caption']);
           }
      }
	  
	  $form->addButton('SUBMIT','submit', 'post');
      $form->addButton('BUTTON','cancel', 'cancel');
	  
	  return $form;
   }
   
}












?>