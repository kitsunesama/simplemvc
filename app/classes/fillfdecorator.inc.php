<?php

class FillFormDecorator extends AbstractFormBuilder {

   protected function internal_build($form, $model) {
   
      
      foreach ($model->get_mapped_data() as $name=>$value) {
	  
         foreach ($form->fieldsets as $key=>$fieldset) {
		 
            if ($fieldset->hasField($name))
                $form->fieldsets[$key]->getField($name)->setValue($value);
         }
                 
          for ($i=0; $i<count($form->hidden_fields); $i++) {
		  
             if ($form->hidden_fields[$i]->_name == $name)
                $form->hidden_fields[$i]->setValue($value);
          }
                 
          for ($i=0; $i<count($form->buttons); $i++) {
		  
             if ($form->buttons[$i]->_name == $name)
                $form->buttons[$i]->setValue($value);
          }
     }
	 
	 return $form;
	 
  }
}











?>