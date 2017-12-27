<?php

 class Cc_mails extends Cc_Settings {
     
     public static function Init(){
          add_action('esig_signature_saved', array(__CLASS__, 'signature_saved'), -8, 1);
          
          add_action('init', array(__CLASS__, 'cc_preview'), -8);
          
          add_filter("can_view_preview_document",array(__CLASS__, 'document_allow'), -8,1);
     }
     
     public static function document_allow($allow){
          if(esigget('cc_user_preview')){
              if(self::is_cc_enabled(esigget('document_id'))){
                  return true;
              }
              
          }
          return $allow;
     }


     public static function cc_preview(){
         if(esigget('ccpreview')){
            $document_id = WP_E_Sig()->document->document_id_by_csum(esigget('csum'));
            wp_redirect(self::cc_preview_url($document_id));
            exit;
         }
         return ;
     }

     public static function signature_saved($PostData) {

            $document_id =$PostData['invitation']->document_id;
            
            $signer_id =$PostData['invitation']->user_id; 
            
            if(!self::is_cc_enabled($document_id)){
                return false;
            }
            // generate an object to pass value in email templates 
            $cc_users = new stdClass();
            
            $cc_users->doc = WP_E_Sig()->document->getDocument($document_id);
            $cc_users->owner_name = self::get_owner_name($cc_users->doc->user_id);
            $cc_users->owner_email = self::get_owner_email($cc_users->doc->user_id); 
            $cc_users->organization_name = self::get_organization_name($cc_users->doc->user_id) ; 
            $cc_users->signers = WP_E_Sig()->signer->get_document_signer_info($signer_id,$document_id);
            $cc_users->signed_link = self::get_cc_preview($cc_users->doc->document_checksum);
            
            $subject = sprintf(__("You have been copied on %s - signed by %s","esig"),$cc_users->doc->document_title,$cc_users->signers->signer_name) ; ;
            
            $notify_template = ESIGN_CC_PATH . '/views/signed-email-template.php';

            $email_temp = WP_E_Sig()->view->renderPartial('',$cc_users, false, '', $notify_template);

            $signers = self::get_cc_information($document_id,false) ; 
            
            foreach ($signers as $user_info) {
                WP_E_Sig()->email->esig_mail($cc_users->owner_name,$cc_users->owner_email,$user_info->email_address, $subject, $email_temp);
                // $this->invitationsController->saveThenSend($invitation, $doc);
            }
        }

       
 }
