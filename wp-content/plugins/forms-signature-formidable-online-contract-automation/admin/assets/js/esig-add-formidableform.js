

(function($){
        

        // next step click from sif pop
        $( "#esig-formidableform-create" ).click(function() {
                 var form_id= $('select[name="esig-formidableform-id"]').val();
                 $("#esig-formidable-form-first-step").hide();
                 // jquery ajax to get form field . 
                 jQuery.post(esigAjax.ajaxurl,{ action:"esig_formidableform_fields",form_id:form_id},function( data ){ 
                                $("#esig-formidable-field-option").html(data);
				},"html");
                  $("#esig-formidableform-second-step").show();                        
  
        });
 
       
        $( "#esig-formidable-insert" ).click(function() {
                 var formid= $('select[name="esig-formidableform-id"]').val();
                 var field_id =$('select[name="esig_formidableform_field_id"]').val();
                 var return_text = '[esigformidable formid="'+ formid +'" field_id="'+ field_id +'" ] ';
		 esig_sif_admin_controls.insertContent(return_text);
            
             tb_remove();
                     
                   
        });
        
        
        //if overflow
        $('#select-formidable-form-list').click(function(){
       
         $(".chosen-drop").show(0, function () { 
	$(this).parents("div").css("overflow", "visible");
	});
            
            
            
        });
	
})(jQuery);



