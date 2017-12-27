<?php

class Access_Control_Setting {

    const ACCESS_CONTROL_META = 'esig_wpaccess_control';

    public static function save_access_meta($document_id, $value) {
        WP_E_Sig()->meta->add($document_id, self::ACCESS_CONTROL_META, json_encode($value));
    }

    /**
     * 
     * @param type $document_id
     * @return object
     */
    public static function get_access_meta($document_id, $return = false) {
        return json_decode(WP_E_Sig()->meta->get($document_id, self::ACCESS_CONTROL_META), $return);
    }

    public static function get_roles_permission_setting($meta) {
        $roles = $meta->esig_access_control_role;
        if (is_array($roles)) {
            return $roles;
        } else {
            return array();
        }
    }

    /**
     * 
     * @param type $document_id
     * @return array
     */
    public static function get_user_permission_settings($document_id) {

        $meta = self::get_access_meta($document_id, true);

        if (is_array($meta['esig_users_permission'])) {
            return $meta['esig_users_permission'];
        }
        return array();
    }

    public static function dashboard_output($status, $document_id, $meta) {


        if (self::is_draft_doc($document_id)) {
            return false;
        }

        if ($status == "required") {
            return self::required_doc_output($document_id, $meta);
        } elseif ($status == "optional") {

            return self::optional_doc_output($document_id, $meta);
        } elseif ($status == "signed") {
            return self::signed_doc_output($document_id, $meta);
        } else {
            return self::all_doc_output($document_id, $meta);
        }
    }

    public static function required_doc_output($document_id, $meta) {

        if (!self::is_required_doc($meta)) {
            return;
        }

        if (self::this_document_signed($document_id)) {
            return;
        }

        return ESIG_ACCESS_CONTROL_Shortcode::esig_doc_dashboard11($document_id, $meta);
    }

    public static function optional_doc_output($document_id, $meta) {

        if (!self::is_optional_doc($meta)) {
            return;
        }

        if (self::this_document_signed($document_id)) {

            return;
        }

        return ESIG_ACCESS_CONTROL_Shortcode::esig_doc_dashboard11($document_id, $meta);
    }

    public static function signed_doc_output($document_id, $meta) {

        /* if (!self::is_signed_doc($document_id)) {
          return;
          }
          if (!self::this_document_signed($document_id)) {
          return;
          } */
        if (!WP_E_Sig()->signature->userHasSignedDocument(self::get_esign_user_id(), $document_id)) {
            return;
        }
        return ESIG_ACCESS_CONTROL_Shortcode::esig_doc_dashboard11($document_id, $meta);
    }

    public static function all_doc_output($document_id, $meta) {

        if (self::is_all_sad_signed($document_id)) {
            return;
        }
        return ESIG_ACCESS_CONTROL_Shortcode::esig_doc_dashboard11($document_id, $meta);
    }

    public static function store_signed_data($document_id) {
        $wp_user_id = get_current_user_id();
        $user_data = get_userdata($wp_user_id);
        $email_address = $user_data->user_email;
        //add_user_meta($email_address , "esig-" . $document_id . "-signed", 1);
        WP_E_Sig()->meta->add($document_id, "esig-access-control-signed", $email_address);
    }

    public static function store_sad_signed_data($wp_user_id, $document_id) {
        update_user_meta($wp_user_id, 'esig-stand-alone-' . $document_id, "signed");
    }

    public static function get_sad_signed_data($wp_user_id, $document_id, $email_address = false) {
        $signed = get_user_meta($email_address, "esig-" . $document_id . "-signed", true);
        if ($signed) {
            return $signed;
        }
        return get_user_meta($wp_user_id, 'esig-stand-alone-' . $document_id, true);
    }

    public static function is_all_sad_signed($document_id) {

        $api = new WP_E_Api();

        $docutmet_status = $api->document->getStatus($document_id);

        if ($docutmet_status == "stand_alone") {

            if (self::this_document_signed($document_id)) {
                return true;
            }
        }
        return false;
    }

    public static function is_draft_doc($document_id) {

        $docutmet_status = WP_E_Sig()->document->getStatus($document_id);

        if ($docutmet_status == "draft") {
            return true;
        }

        if ($docutmet_status == "stand_alone") {
            $pageId = esig_sad_document::get_instance()->get_sad_page_id($document_id);
            $maxDocId = esig_sad_document::get_instance()->get_sad_id($pageId);
            if ($document_id != $maxDocId) {
                return true;
            }
        }

        return false;
    }

    public static function is_signed_doc($document_id) {

        $docutmet_status = WP_E_Sig()->document->getStatus($document_id);
        $docType = WP_E_Sig()->document->getDocumenttype($document_id);

        if ($docType == 'normal') {
            if (WP_E_Sig()->document->getSignedresult($document_id)) {
                return true;
            }

            if (!WP_E_Sig()->invite->get_Invite_Hash(self::get_esign_user_id(), $document_id)) {
                return true;
            }
            if (WP_E_Sig()->signature->userHasSignedDocument(self::get_esign_user_id(), $document_id)) {
                return true;
            }
        }
        if ($docutmet_status == "signed") {
            return true;
        }
        return false;
    }

    public static function is_required_doc($meta) {

        if ($meta->esig_document_permission == "required") {
            return true;
        }
        return false;
    }

    public static function is_optional_doc($meta) {

        if ($meta->esig_document_permission == "optional") {
            return true;
        }
        return false;
    }

    public static function this_document_signed($document_id) {

        $wp_user_id = get_current_user_id();
        $user_data = get_userdata($wp_user_id);
        $email_address = $user_data->user_email;

        $document_type = WP_E_Sig()->document->getDocumenttype($document_id);

        if ($document_type == "stand_alone") {

            if (self::is_user_signed_already($email_address, $document_id)) {
                return true;
            } else {

                if (self::is_signed_doc($document_id)) {
                    return true;
                }
                return false;
            }
        } elseif ($document_type == "normal") {

            if (WP_E_Sig()->document->getSignedresult($document_id)) {
                return true;
            }

            if (!WP_E_Sig()->invite->get_Invite_Hash(self::get_esign_user_id(), $document_id)) {
                return true;
            }

            if (WP_E_Sig()->signature->userHasSignedDocument(self::get_esign_user_id(), $document_id)) {
                return true;
            }
            $docutmet_status = WP_E_Sig()->document->getStatus($document_id);
            if ($docutmet_status == 'signed') {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function is_user_signed_already($email_address, $document_id) {

        $signed = self::get_sad_signed_data(self::wordpressUserId(), $document_id, $email_address);
        if ($signed) {
            return true;
        } else {
            $meta_data = WP_E_Sig()->meta->get($document_id, 'esig-access-control-signed');
            if ($meta_data == $email_address) {
                return true;
            }
        }
        return false;
    }

    public static function esig_access_control_enabled($meta) {

        if (!is_object($meta)) {
            return false;
        }

        $esig_required_wpmember = (isset($meta->esig_required_wpmember)) ? $meta->esig_required_wpmember : null;

        if ($esig_required_wpmember) {
            return true;
        } else {

            return false;
        }
    }

    public static function is_access_control_enabled($document_id) {

        $meta = self::get_access_meta($document_id);

        if (!is_object($meta)) {
            return false;
        }

        $esig_required_wpmember = (isset($meta->esig_required_wpmember)) ? $meta->esig_required_wpmember : null;

        if ($esig_required_wpmember) {
            return true;
        } else {

            return false;
        }
    }

    public static function get_esign_user_id() {

        $api = new WP_E_Api();
        $wp_user_id = get_current_user_id();
        $user_data = get_userdata($wp_user_id);
        $email_address = $user_data->user_email;
        $esign_user_id = $api->user->getUserID($email_address);
        return $esign_user_id;
    }

    public static function wordpressUserId() {
        return get_current_user_id();
    }

    /*     * *
     *  Checking current user role access . 
     *  @return bolean 
     *  @Since 1.3.1
     */

    public static function esig_is_user_access($wp_user_id, $meta, $document_id = false) {

        $user_data = get_userdata($wp_user_id);
        $current_role = implode(', ', $user_data->roles);

        if (in_array($current_role, self::get_roles_permission_setting($meta))) {
            return true;
        }
        //$users = self::get_user_permission_settings($document_id);
        if (in_array($wp_user_id, self::get_user_permission_settings($document_id))) {
            return true;
        }


        return false;
    }

    public static function is_document_access($wp_user_id, $document_id) {

        $document_type = WP_E_Sig()->document->getDocumenttype($document_id);

        $document_status = WP_E_Sig()->document->getStatus($document_id);

        if ($document_status == "signed") {
            return false;
        }

        if ($document_type == "normal") {

            $user_data = get_userdata($wp_user_id);
            $user_email = $user_data->user_email;
            $esign_user_id = WP_E_Sig()->user->getUserID($user_email);

            if (WP_E_Sig()->signer->exists($esign_user_id, $document_id)) {
                return true;
            }
        } elseif ($document_type == "stand_alone") {
            return true;
        }
        return false;
    }

    /*     * *
     * getting all access control documents 
     */

    public static function get_ac_documents() {
        return WP_E_Sig()->meta->getall_bykey("esig_wpaccess_control");
    }

}
