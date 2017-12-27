</form>	
				
    
                        <?php if (array_key_exists('form_tail', $data)) { echo $data['form_tail']; } ?>
				
				
<div class="af-inner_edit" id="standard_view_popup_edit" style="display:none;">
    <style>#TB_ajaxContent {max-width:500px !important;}</style>
    <div class="invitations-container_ajax">
        <div align="center">
            <img src="<?php if (array_key_exists('ESIGN_ASSETS_DIR_URI', $data)) { echo $data['ESIGN_ASSETS_DIR_URI'];} ?>/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;">
        </div>
        
        <h2 class="esign-form-header">
            <?php _e('Who needs to sign this document?', 'esig'); ?>
        </h2>
        
        <div id="esig-signer-edit-wrapper" style="width:auto;max-width: 530px;">
                
        </div>
      
        <div align="center">
        <input type="button" value="Save Changes" class="submit button button-primary button-large" id="submit_signer_save" name="signersave">
        </div>
        
    </div>  
</div>		
	
		
		
		 

<!--E-signature dialog content here -->	
<div id="esig-dialog-content" style="display: none;"> </div>
