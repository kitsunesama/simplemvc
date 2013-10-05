<?php

class JsFormDecorator extends AbstractFormBuilder {
   
   
   protected function internal_build($form, $model) {
   
      foreach ($model->clientside_validator->get_rules() as $field_name => $rule) {
	  
	     $form->set_js_rule($field_name, $rule);
	  }
   
      $fn = create_function('$a,$key','echo "\'".$key."\' : \'".$a."\',";');
	  
      $fn4 = create_function('$a,$key',
                      '  echo "\'".$key."\' : {
                           \'jfunc\' : \'".$a[0]["function"]."\',
                           \'jmessage\' : \'".$a[0]["message"]."\'
                          },";');
                         
      ob_start();              
 ?>
 
<style type="text/css">
   
     .tooltip {
        position:absolute;
        padding:10px;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -o-border-radius: 5px;
        font-size:12px;
        border: solid 1px #999;
        background: #fffff9;
     }
                   
     .errorhint {
        text-align: center;
        color: red;
      }
</style>

<script type="text/javascript">
               
   var data_%id% = {
       'hints':{<?php $field_hints=$form->get_field_hints(); array_walk($field_hints, $fn);?> 'inf': 4},
	   
       'mandatory':{<?php $js_rules=$form->get_js_rules(); array_walk($js_rules, $fn4); ?> 'inf': 4},
    }
                   
	//-----------------------------
    var funcs_%id% = {
    //-----------------------------
    init : function () {
                     
       var frm = jQuery('#%id%');
       frm.bind ('submit',
                    funcs_%id%.submit);
					
	   frm.find('input[name="cancel"]').bind('click',
	       funcs_%id%.cancel_click
       );	   
                         
       var hints = data_%id%.hints;
	   
       for (var key in hints) {
                         
          if (key=='inf')
              continue;
                             
          el = jQuery('input[name="'+key+'"],select[name="'+key+'"],textarea[name="'+key+'"]',frm);
                                 
          el.each (function() {
             jQuery(this).hover(funcs_%id%.hover_in(hints[key]),funcs_%id%.hover_out);
             jQuery(this).bind('click',funcs_%id%.hover_out);
             jQuery(this).bind('click',funcs_%id%.clear_errors);
          })
        }
     },
	 
     //-----------------------------
     clear_errors : function () {
                   
        var frm = jQuery('#%id%');
           jQuery('.errorhint',frm).remove();
     },
	 
     //-----------------------------
     submit : function() {
                   
        funcs_%id%.clear_errors();    
        var correct = true;
        var mandatory = data_%id%.mandatory;
                         
        for (var key in mandatory) {                           
           if (key=='inf')
              continue;
                                         
           var el = jQuery('input[name="'+key+'"],select[name="'+key+'"],textarea[name="'+key+'"]',jQuery(this));
           var func = mandatory[key].jfunc;
                                 
           el.each ( function() {
		
		      var zval = jQuery(this).val();
              var fn4 ='('+func+')(\''+zval.replace(/'/g, "\\'")+'\')';           
              var tezz = eval(fn4);
                               
              if (tezz==false) {
                 jQuery(this).after(jQuery('<div/>',{'class':'errorhint'}).html(mandatory[key].jmessage));
                 correct = false;
              }
		   });                           
         }
		 return correct;
     },
	 //-----------------------------
	 cancel_click: function() {
	    
	    location.assign("<?php echo $form->cancel_route;?>");
	 },
     //-----------------------------
     hover_in: function(hint) {
                   
        return function() {
           funcs_%id%.show_hint(this, hint);
        }
     },  
	 
     //-----------------------------
     hover_out: function() {
                   
        if (this.tmin) {
           clearTimeout(this.tmin);    
        }
     },
	 
     //-----------------------------
     show_hint: function(el, hint) {
	 
        if (!el.hintdiv) {
		
           el.hintdiv = jQuery('<div/>',{'class':'tooltip'}).css('display','none').html(hint);
           jQuery(document.body).append(el.hintdiv);     
           el.hintdiv.css('top', jQuery(el).offset().top - 20);  
           el.hintdiv.css('left', jQuery(el).offset().left + 400);                                         
        }
		
        if (el.vis==true) {
           return;
        }      
		
        el.tmin = setTimeout (
		
		   function () {
		   
              el.vis = true;
              el.hintdiv.fadeIn('slow');  
 
              el.tmout = setTimeout( 
		         function() {
                    el.hintdiv.fadeOut('slow');
                    el.vis = false;
              }, 
		      3000);         
           }, 
		1000);
	 }
     
  }
                   
//-----------------------------
jQuery(document).ready (
     funcs_%id%.init
)
</script>
<?php
               
   $base_action = ob_get_clean();
   $base_action = str_replace(array('%id%'),array($form->id), $base_action);
               
   $form->set_script('base', array ('handler'=>'base', 'script'=>$base_action));
   return $form;
   
   }
   
}



?>