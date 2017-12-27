<?php

if (!class_exists('ESIG_FORMIDABLEFORM_SETTING')):

    class ESIG_FORMIDABLEFORM_SETTING {
    
    
        const FF_COOKIE = 'esig-formidable-temp-data';
        const FF_FORM_ID_META = 'esig_formidable_form_id';
        const FF_ENTRY_ID_META = 'esig_formidable_entry_id';
        private static $tempCookie = null ;
        
        
        public static function is_ff_requested_agreement($document_id){
             $ff_form_id = WP_E_Sig()->meta->get($document_id,self::FF_FORM_ID_META);
             $ff_entry_id = WP_E_Sig()->meta->get($document_id,self::FF_ENTRY_ID_META);
             if($ff_form_id && $ff_entry_id){
                 return true;   
             }
             return false;
        }
        
        public static function is_ff_esign_required(){
            if(self::get_temp_settings()){
                return true;
            }
            else {
                return false;
            }
        }
        
         public static function get_invite_url($invite_hash){
              $document_checksum = WP_E_Sig()->document->document_checksum_by_id(WP_E_Sig()->invite->getdocumentid_By_invitehash($invite_hash));
              return WP_E_Sig()->invite->get_invite_url($invite_hash,$document_checksum);
        }
        
        public static function get_temp_settings(){
             if(!empty(self::$tempCookie)){
                 return json_decode(self::$tempCookie,true);
             }           
             if(ESIG_COOKIE(self::FF_COOKIE))
             {
                 return json_decode(stripslashes(ESIG_COOKIE(self::FF_COOKIE)),true);
             }
             return false;
        }
        
        public static function save_temp_settings($value){
            $json = json_encode($value);
            esig_setcookie(self::FF_COOKIE,  $json ,600);
            // for instant cookie load. 
            $_COOKIE[self::FF_COOKIE] = $json;
           self::$tempCookie = $json;
           
        }
        
        public static function delete_temp_settings(){
            esig_unsetcookie(self::FF_COOKIE);
        }
        
        public static function save_esig_ff_meta($meta_key, $meta_index, $meta_value) {
            
            $temp_settings = self::get_temp_settings();
            if (!$temp_settings) {
                $temp_settings= array();
                $temp_settings[$meta_key] = array($meta_index => $meta_value);
                
                self::save_temp_settings($temp_settings);
            } else {
                
                if (array_key_exists($meta_key, $temp_settings)) {
                    $temp_settings[$meta_key][$meta_index] = $meta_value;
                    self::save_temp_settings($temp_settings);
                } else {
                    $temp_settings[$meta_key] = array($meta_index => $meta_value);
                    self::save_temp_settings($temp_settings);
                }
            }
        }
        
        
        public static function save_entry_value($documentId,$entry) {
            WP_E_Sig()->meta->add($documentId, "esig_formidable_submission_value", json_encode($entry->metas));
        }
        
        public static function get_submission_value ($documentId,$field_id){
           $formidableValue = json_decode(WP_E_Sig()->meta->get($documentId, "esig_formidable_submission_value"),true);
           if (array_key_exists($field_id, $formidableValue)) {
                return $formidableValue[$field_id];
            }
       }
        
        public static function field_type($fieldId){
                 $type = FrmDb::get_var( 'frm_fields', array( 'id' => $fieldId ), 'type' );
                 return $type;
        }
        
      public static function generate_value($documentId,$formId,$fieldId){
                    $fieldType = self::field_type($fieldId);
                    $value = self::get_submission_value($documentId,$fieldId);
                    $table_class = apply_filters( 'frm_entries_list_class', 'FrmEntriesListHelper' );
                   
                    switch ($fieldType) {
                        case 'checkbox':
                           return self::get_checkbox($value);
                           break;
                       case 'email':
                           return '<a href="mailto:'. $value.'" target="_blank">' .$value. '</a>';
                           break;
                       case 'url':
                           return '<a href="'. $value.'" target="_blank">' .$value. '</a>';
                           break;
                       case 'date':
                           $newDate = date("m-d-Y", strtotime($value));
                           return $newDate;
                           break;
                        default :
                        return $value; 
                    }
        }
        
        public static function get_checkbox($value){
               
              if(!$value){
                   return false ;
               }
                
               $html = '';
               
               if (is_array($value)) {
                    foreach ($value as $val) {
                        $html .= '<input type="checkbox" disabled readonly checked="checked"> ' . $val . "<br>";
                    }
                } else {
                    $html .='<input type="checkbox" disabled readonly checked="checked"> ' . $value . "<br>";
                }
               
               return $html;
        }
       
        public static function display_value($formidableValue,$underLineData) {
             $result = '';
            if ($underLineData == "underline") {
                if(is_array($formidableValue)){
                     foreach($formidableValue as $val){
                         $result .= '<u>' . $val . '</u>';
                     }
                }
                else {
                     $result = '<u>' . $formidableValue . '</u>';
                 }
            } else {
                if(is_array($formidableValue)){
                     foreach($formidableValue as $val){
                         $result .= $val;
                     }
                     
                 }else {
                     $result = $formidableValue;
                 }
               
            }
            return $result;
                    
               
        }
     }
endif;