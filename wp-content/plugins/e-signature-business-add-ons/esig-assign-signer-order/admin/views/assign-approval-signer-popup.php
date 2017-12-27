


<div id="approval_signer_view_popup" class="esign-form-panel" style="display:none">

    <span class="approval-invitations-container" >	
        <div align="center"><img src=" <?php echo(ESIGN_ASSETS_DIR_URI) ?>/images/logo.png" width="200px" height="45px" alt="Sign Documents using WP E-Signature" width="100%" style="text-align:center;"></div>
        <h2 class="esign-form-header"><?php _e('Who needs to approve this document?', 'esig-order'); ?></h2>

        <div class="af-inner" style="max-width:440px;">

            <div id="recipient_approval_signer">

                <div id="signer_main">

                    <input type="text" class="recipient_fnames" name="approval_signer_fname[]"  readonly="" placeholder="Signer 1(from website)" />
                    <input type="text" class="recipient_emails" name="approval_signer_emails[]" readonly="" placeholder="Signer 1(from website)"/>
                    <!--<input type="text" name="recipient_lnames[]" placeholder="Signers last name" /> -->
                </div>

                <?php
                $document_id = esigget('document_id');
                $count = 0 ; 
                $api = new WP_E_Api();
                
                $signer_order = $api->meta->get($document_id, 'esig_signer_order_sad');
                $signer_order_checked = (isset($signer_order) && $signer_order == 'active') ? "checked" : "";
                if ($document_id) {
                    

                    $signers = json_decode($api->meta->get($document_id, 'esig_assign_approval_signer_save'), true);
                    
                    
                    for ($i = 1; $i < count($signers[1]); $i++) {
                        $email_address = $signers[1][$i];
                        $signer_fname = $signers[0][$i];
                        $j = $i + 1;
                        ?>
                
                        <div id="signer_main"  <?php  if ($signer_order_checked =="checked"): ?> style="width: 450px;" <?php endif; ?>>
                           <?php  if ($signer_order_checked =="checked"){ ?>
                            <span id="signer-sl" class="signer-sl"><?php echo $j; ?></span><span class="field_arrows"><span id="esig_signer_up"  class="up"> &nbsp; </span><span id="esig_signer_down"  class="down"> &nbsp; </span></span><?php } ?>
                            <input type="text" class="recipient_fnames" name="approval_signer_fname[]"   value="<?php echo $signer_fname; ?>" />
                            <input type="text" class="recipient_emails" name="approval_signer_emails[]"   value="<?php echo $email_address; ?>"  />
                            <span id="esig-del-approval-signer" class="deleteIcon"></span>
                            <!--<input type="text" name="recipient_lnames[]" placeholder="Signers last name" /> -->
                        </div>
                        <?php
                        $count++ ; 
                          
                    }
                }
                ?>

            </div><!-- [data-group=recipient-emails] -->

            <div class="esig-signer-container">
                
                 <?php 
                   $display = ($count>0)?'block;float:left;':'none';
                   
                  
                 ?>
                

                <span class="esig-signer-left" id="esign-approval-signer-order" style="display: <?php echo $display; ?>;" >
                    
                    <input type="checkbox" id="esign_assign_approval_signer_order" name="esign_assign_approval_signer_order" value="1" <?php echo $signer_order_checked  ; ?> >
<?php _e('Assign signer order', 'esig'); ?>
                    
                </span>

                <span class="esig-signer-right"><a href="#" id="add-approval-signer"><?php _e('+ Add Signer', 'esig-order'); ?></a></span>
            </div> 

        </div>

    </span>
    <p align="center">
        <input type="button" value="Save" class="submit button button-primary button-large" id="submit_approval_signer_save" name="signersave">
    </p>


    <span class="settings-title"></span>
</div>
