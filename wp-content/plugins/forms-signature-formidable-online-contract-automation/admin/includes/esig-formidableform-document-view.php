<?php
/**
 *
 * @package ESIG_WPFORM_DOCUMENT_VIEW
 * @author  Arafat Rahman <arafatrahmank@gmail.com>
 */



if (! class_exists('esig-formidableform-document-view')) :
class esig_formidableform_document_view {
    
    
            /**
        	 * Initialize the plugin by loading admin scripts & styles and adding a
        	 * settings page and menu.
        	 * @since     0.1
        	 */
        	final function __construct() {
                        
        	}
        	
        	/**
        	 *  This is add document view which is used to load content in 
        	 *  esig view document page
        	 *  @since 1.1.0
        	 */
        	
        	final function esig_formidableform_document_view()
        	{
        	    
        	    if(!function_exists('WP_E_Sig'))
                                return ;
                    
                    
                    
                    
        	    
        	   
        	    $assets_dir = ESIGN_ASSETS_DIR_URI;
        	    
                    
        	   $more_option_page = ''; 
        	   
        	    
        	    $more_option_page .= '<div id="esig-formidable-option" class="esign-form-panel" style="display:none;">
        	        
        	        
                	               <div align="center"><img src="' . $assets_dir .'/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;"></div>
                    			
                                    
                    				<div id="esig-formidable-form-first-step">
                        				
                                        	<h3 class="esign-form-header">'.__('What Are You Trying To Do?', 'esig').'</h3>
                                            	
                        				<p id="create_formidableform" align="center">';
                                	    
                                	    $more_option_page .=	'
                        			
                        				<p id="select-formidable-form-list" align="center">
                                	    
                        		        <select data-placeholder="Choose a Option..." class="chosen-select" tabindex="2" id="esig-formidableform-id" name="esig-formidableform-id">
                        			     <option value="sddelect">'.__('Select a Formidableform', 'esig').'</option>';
                                            
                                            $forms = FrmForm::get_published_forms();
                                            
                                	    foreach($forms as $form)
                                	    {
                                             
                                	        $more_option_page .=	'<option value="'. $form->id . '">'.$form->name.'</option>';
                                	    }
                                            
                                           
                                	    $more_option_page .='</select>
                                	    
                        				</p>
                         	  
                                	    </p>
                                	    
                                        <p id="upload_formidableform_button" align="center">
                                           <a href="#" id="esig-formidableform-create" class="button-primary esig-button-large">'.__('Next Step', 'esig').'</a>
                                         </p>
                                     
                                    </div>  <!-- Frist step end here  --> ';
                            
                                    
                 $more_option_page .='<!-- formidableform  second step start here -->
                                            <div id="esig-formidableform-second-step" style="display:none;">
                                            
                                        	<h4 class="esign-form-header">'.__('What Formidable form field data would you like to insert?', 'esig').'</h4>
                                            
                                            <p id="esig-formidable-field-option" align="center">
                               



                                             </p>
                                            
                                            
                                             <p id="upload_formidableform_button" align="center">
                                           <a href="#" id="esig-formidable-insert" class="button-primary esig-button-large" >'.__('Add to Document', 'esig').'</a>
                                         </p>
                                            
                                            </div>
                                    <!-- formidableform form second step end here -->';           
                                    
                                    
        	    
        	    $more_option_page .= '</div><!--- formidableform option end here -->' ;
        	    
        	    
        	    return $more_option_page ; 
        	}
        	
        	
	   
    }
endif ; 

