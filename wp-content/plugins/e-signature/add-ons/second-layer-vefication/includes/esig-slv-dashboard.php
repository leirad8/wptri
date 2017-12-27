<?php

if (!class_exists('Esig_Slv_Dashboard')):

    class Esig_Slv_Dashboard extends Esig_Slv_Settings {

        public static function Init() {

            add_action("esig_footer", array(__CLASS__, "enqueue_scripts"));

            // add_action("esig_document_loading",array(__CLASS__,"esig_verify_access"),-9,1);
            add_action("esig_document_complate", array(__CLASS__, "sad_document_complete"), 10, 1);
            // add security lebels 
            add_filter("esig_security_levels", array(__CLASS__, "security_levels"), 10, 2);
            add_filter('esig_check_referer', array(__CLASS__, 'allow_set_password'), 10,2);
        }

        final static function allow_set_password($ret, $method) {
           
            if ($method=='slv_set_password') {
                
                $ret = true;
            }
            if ($method=='slv_reset_password') {
                
                $ret = true;
            }
            return $ret;
        }

        final static function security_levels($security_lebels, $document_id) {
            if (self::is_slv_enabled($document_id)) {
                $security_lebels .= " , " .  __("Access Code", "esig");
            }
            return $security_lebels;
        }

        final static function enqueue_scripts() {
            echo "<script type='text/javascript' src='" . ESIGN_SLV_URL . "/assets/js/esig-slv-dashboard.js?ver=1.0.0'></script>";
        }

        final static function sad_document_complete($args) {

            $sad_doc_id = $args['sad_doc_id'];

            if (self::is_slv_enabled($sad_doc_id)) {

                $document_id = $args['invitation']->document_id;
                $invite_hash = $args['invitation']->invite_hash;
                $email_address = self::get_email_address($invite_hash);
                $access_code = self::get_access_code($sad_doc_id, 'stand-alone');
                // saving new document details . 
                self::enable_slv($document_id);
                self::set_access_code($document_id, $email_address, $access_code);
            }
        }

        public static function esig_verify_access($invite_hash, $checksum) {


            $slv_data = new stdClass();

            $document_id = WP_E_Sig()->document->document_id_by_csum($checksum);
            
            $email_address = self::get_email_address($invite_hash);

            // asign global variable properties 
            $slv_data->document_id = $document_id;
            $slv_data->email_address = $email_address;
            $slv_data->invite_hash = $invite_hash;
            $slv_data->checksum = $checksum;

            $GLOBALS['slv_data'] = $slv_data;

            if (self::is_access_code_enabled($document_id, $email_address)) {

                if (self::is_already_logged_in($checksum)) {
                    return false;
                }
                //self::store_document_id_temp($checksum);
                $template_data = array(
                    "message" => ''
                );
                $esig_shortcode = new WP_E_Shortcode();
                $esig_shortcode->displayDocumentToSign(null, 'login-form', $template_data);
                return true;
            } else {

                return false;
            }
        }

        public function esig_verify_access_code() {


            $validation = new WP_E_Validation();
            $email_address = ESIG_POST('esig_email_address');
            $access_code = $validation->esig_clean(ESIG_POST('esig_access_code'));
            $checksum = ESIG_POST('checksum');
            $document_id = WP_E_Sig()->document->document_id_by_csum($checksum);
            if (!$validation->esig_valid_email($email_address)) {
                _e("The E-mail address you entered is not valid.", "esig");
                return false;
            }

            // checking is access code used or not 
            if (self::is_access_code_used($document_id, $email_address)) {

                $old_password = self::get_slv_password($document_id, $email_address);
                if ($old_password == $access_code) {

                    esig_setcookie("esig-slv-" . $checksum, "Yes", 1 * 60 * 60);

                    echo "verified";
                    return true;
                } else {

                    _e("<span class='error-email-password'>The Password or email that you entered is incorrect. Please try again or contact the document administrator.</span>", "esig");
                }
            } else {
                // checking and verifying access code 
                $old_code = self::get_access_code($document_id, $email_address);
                if ($access_code == $old_code) {
                    echo "display";
                    return false;
                } else {
                    // _e("<span class='icon-esig-alert'></span><span class='error-email-password'>The Access Code or email that you entered is incorrect. Please try again or contact the document administrator.</span>", "esig");
                }
            }

            _e("<span class='icon-esig-alert esigalert'></span><span class='error-email-password'>The Password or email that you entered is incorrect. Please try again or contact the document administrator.</span>", "esig");

            return false;
        }

        public function slv_set_password() {

            $validation = new WP_E_Validation();
            $esig_slv_password = $validation->esig_clean(ESIG_POST('esig_slv_password'));
            $esig_slv_confirm_password = $validation->esig_clean(ESIG_POST('esig_slv_confirm_password'));

            if (empty($esig_slv_password) || empty($esig_slv_confirm_password)) {
                _e("<span class='icon-esig-alert'></span><span  >Please enter your password</span>", "esig");
                return false;
            }

            if ($esig_slv_password != $esig_slv_confirm_password) {
                _e("<span class='icon-esig-alert'></span><span class='error-password'>The Password do not match. Thats OK though.... <br> Type each password carefully and try again.</span>", "esig");
                return false;
            }

            $invite_hash = ESIG_POST('invite_hash');
            $checksum = ESIG_POST('checksum');

            // checking invite hash and checksum 
            if (!isset($invite_hash) && !isset($checksum)) {
                _e("<span style='' class='document-error'>Document is not found</span>", "esig");
                return false;
            }
            // setting password here 
            $email_address = self::get_email_address($invite_hash);

            $document_id = WP_E_Sig()->document->document_id_by_csum($checksum);
            $user_id = WP_E_Sig()->user->getUserID($email_address);
            // saving password 
            self::set_slv_password($document_id, $email_address, $esig_slv_password);
            self::set_access_code_used($document_id, $email_address);
            // setting remember cookie 
            esig_setcookie("esig-slv-" . $checksum, "Yes", 1 * 60 * 60);
            // recording password set event. 
            $signer_name = WP_E_Sig()->user->get_esig_signer_name($user_id, $document_id);
            self::access_code_event_record($signer_name, $email_address, $document_id);

            echo "done";
            return true;
        }

        public function slv_reset_password() {

            $validation = new WP_E_Validation();
            $esig_slv_reset_address = $validation->esig_clean(ESIG_POST('esig_slv_reset_address'));

           $invite_hash = ESIG_POST('invite_hash');
            $checksum = ESIG_POST('checksum');

            // checking invite hash and checksum 
            if (!isset($invite_hash) && !isset($checksum)) {
                _e("<span class='error-reset'>Document is not found</sapn>", "esig");
                return false;
            }

            $email_address = self::get_email_address($invite_hash);
            $document_id = WP_E_Sig()->document->document_id_by_csum($checksum);

            if ($esig_slv_reset_address != $email_address) {
                _e("<span class='error-reset'>E-mail is not match</sapn>", "esig");
                return false;
            }

            // send password to users 
            $mailsent = self::send_password($document_id, $email_address);
            if ($mailsent) {
                echo "done";
            } else {
                _e("<span class='sending-error'>Error in sending password </sapn>", "esig");
            }

            return false;
        }

        public static function send_password($document_id, $email_address) {

            $document = WP_E_Sig()->document->getDocument($document_id);
            $admin_user = WP_E_Sig()->user->getUserByWPID($document->user_id);
            $sender = $admin_user->first_name . " " . $admin_user->last_name;

            $subject = $document->document_title . __(" - Password by ","esig") . $sender;

            $message = __(' Your Document access password is : ','esig') . self::get_slv_password($document_id, $email_address);

            $mailsent = WP_E_Sig()->email->send(array(
                'from_name' => $sender, // Use 'posts' to get standard post objects
                'from_email' => $admin_user->user_email,
                'to_email' => $email_address,
                'subject' => $subject,
                'message' => $message,
                'attachments' => false,
                'document' => $document,
            ));


            return $mailsent;
        }

    }

endif;