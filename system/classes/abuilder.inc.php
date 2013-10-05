<?php

abstract class AbstractFormBuilder implements IDecorator {

   protected $in_builder;

   public function wrap($obj) {
   
      $this->in_builder = $obj;
   }
   
   public final function build($form, $model) {
      
	  if (isset($this->in_builder)) {
	      $form = $this->in_builder->build($form, $model);
	  }
	  
	  return $this->internal_build($form, $model);
   }
   
   abstract protected function internal_build($form, $model);
}








?>