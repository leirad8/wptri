<?php

/**
 *
 * @package ESIG_FORMIDABLEFORM_Admin
 * @author  Arafat Rahman <arafatrahmank@gmail.com>
 */
if (!class_exists('ESIG_FORMIDABLEFORM_Admin')) :

    class ESIG_FORMIDABLEFORM_Admin extends ESIG_FORMIDABLEFORM_SETTING {

        /**
         * Instance of this class.
         * @since    1.0.1
         * @var      object
         */
        protected static $instance = null;
        public $name;

        /**
         * Slug of the plugin screen.
         * @since    1.0.1
         * @var      string
         */
        protected $plugin_screen_hook_suffix = null;

        /**
         * Initialize the plugin by loading admin scripts & styles and adding a
         * settings page and menu.
         * @since     0.1
         */
        public function __construct() {
            /*
             * Call $plugin_slug from public plugin class.
             */
            $plugin = ESIG_FORMIDABLEFORM::get_instance();
            $this->plugin_slug = $plugin->get_plugin_slug();
            $this->document_view = new esig_formidableform_document_view();

            add_action('init', array($this, 'wpform_wpesignature_init_text_domain'));
            add_action('init', array($this, 'registerStyle'), -65);
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'), 999);
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            add_filter('esig_sif_buttons_filter', array($this, 'add_sif_formidable_buttons'), 15, 1);
            add_filter('esig_text_editor_sif_menu', array($this, 'add_sif_formidable_text_menu'), 15, 1);
            add_action('wp_ajax_esig_formidableform_fields', array($this, 'esig_formidableform_fields'));

            add_filter('esig_admin_more_document_contents', array($this, 'document_add_data'), 10, 1);
            add_shortcode('esigformidable', array($this, "render_shortcode_esigformidable"));
            add_action('admin_init', array($this, 'esig_almost_done_formidableform_settings'));
            add_action('frm_registered_form_actions', array($this, 'register_actions'), 100);
            add_action('frm_trigger_esig_create_action', array($this, 'esig_formidable_after_entry'), 10, 3);
            add_filter('show_sad_invite_link', array($this, 'show_sad_invite_link'), 10, 3);
            add_action('admin_notices', array($this, 'esig_formidableform_addon_requirement'));
            add_action('admin_menu', array($this, 'esig_formidableform_adminmenu'));
            add_filter('esig_invite_not_sent', array($this, 'show_invite_error'), 10, 2);
            add_action('media_buttons', 'FrmFormsController::insert_form_button');
            add_action('wp_esignature_loaded', array($this, 'remove_wp_addform_button'));
            add_action('esig_signature_loaded', array($this, 'after_sign_check_next_agreement'), 99, 1);
            add_action('wp_esignature_loaded', array($this, 'remove_ff_addform_button'), 15);
            add_action('frm_after_entry_processed', array($this, 'esig_reroute_confirmationconfirmation'), 10, 1);
        }

        public function esig_reroute_confirmationconfirmation($args) {


            $settings = self::get_temp_settings();

            if (!empty($settings) && is_array($settings)) {
                $settings = array_reverse($settings);
                foreach ($settings as $inviteHash => $array) {

                    $signed = esigget('signed', $array);
                    if ($signed == 'no') {
                        $docId = WP_E_Sig()->invite->getdocumentid_By_invitehash($inviteHash);
                        $docCheckSum = WP_E_Sig()->document->document_checksum_by_id($docId);
                        $inviteUrl = WP_E_Sig()->invite->get_invite_url($inviteHash, $docCheckSum);
                        self::save_esig_ff_meta($inviteHash, 'signed', 'yes');
                        wp_redirect($inviteUrl);
                        exit;
                    }
                }
            }
        }

        public function remove_ff_addform_button() {

            if (!function_exists('WP_E_Sig'))
                return;

            $document_id = (isset($_GET['document_id'])) ? $_GET['document_id'] : null;

            $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
            $esig_type = isset($_GET['esig_type']) ? $_GET['esig_type'] : null;

            if ($document_type == 'normal' || $document_type == "stand_alone" || $esig_type == "sad" || $esig_type == "template") {
                remove_action('media_buttons', 'FrmFormsController::insert_form_button');
            }
        }

        final function after_sign_check_next_agreement($args) {

            $document_id = $args['document_id'];

            if (!ESIG_FORMIDABLEFORM_SETTING::is_ff_requested_agreement($document_id)) {
                return;
            }
            if (!ESIG_FORMIDABLEFORM_SETTING::is_ff_esign_required()) {

                return;
            }

            $invite_hash = WP_E_Sig()->invite->getInviteHash_By_documentID($document_id);
            ESIG_FORMIDABLEFORM_SETTING::save_esig_ff_meta($invite_hash, "signed", "yes");

            $temp_data = array_reverse(ESIG_FORMIDABLEFORM_SETTING::get_temp_settings());
            

            //$t_data = krsort($temp_data);

            foreach ($temp_data as $invite => $data) {
                if ($data['signed'] == "no") {
                    $invite_url = ESIG_FORMIDABLEFORM_SETTING::get_invite_url($invite);
                    wp_redirect($invite_url);
                    exit;
                } else {
                    ESIG_FORMIDABLEFORM_SETTING::delete_temp_settings();
                }
            }
        }

        final function registerStyle() {

            wp_register_style('esig-formidable-icon-css', plugins_url('assets/css/esig-formidable-styles.css', __FILE__), array(), '0.1.0', 'all');
        }

        public function remove_wp_addform_button() {

            if (!function_exists('Esign_core_load'))
                return;

            $document_id = (isset($_GET['document_id'])) ? $_GET['document_id'] : null;

            $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
            $esig_type = isset($_GET['esig_type']) ? $_GET['esig_type'] : null;

            if ($document_type == 'normal' || $document_type == "stand_alone" || $esig_type == "sad" || $esig_type == "template") {
                if (current_user_can('frm_view_forms')) {
                    remove_action('media_buttons', 'FrmFormsController::insert_form_button');
                }
            }
        }

        public function add_sif_formidable_buttons($sif_menu) {
            $esig_type = isset($_GET['esig_type']) ? $_GET['esig_type'] : null;
            $document_id = isset($_GET['document_id']) ? $_GET['document_id'] : null;
            if (empty($esig_type) && !empty($document_id)) {
                $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
                if ($document_type == "stand_alone") {
                    $esig_type = "sad";
                }
            }

            if ($esig_type != 'sad') {
                return $sif_menu;
            }
            $sif_menu .=' {text: "Formidable Form Data",value: "ff", onclick: function () { tb_show( "+ Formidable form option", "#TB_inline?width=450&height=300&inlineId=esig-formidable-option");}},';
            return $sif_menu;
        }

        public function add_sif_formidable_text_menu($sif_menu) {

            $esig_type = esigget('esig_type');
            $document_id = esigget('document_id');

            if (empty($esig_type) && !empty($document_id)) {
                $document_type = WP_E_Sig()->document->getDocumenttype($document_id);
                if ($document_type == "stand_alone") {
                    $esig_type = "sad";
                }
            }

            if ($esig_type != 'sad') {
                return $sif_menu;
            }
            $sif_menu['Formidable'] = array('label' => "Formidable Form Data");
            return $sif_menu;
        }

        public function esig_formidableform_fields() {

            if (!function_exists('WP_E_Sig'))
                return;
            $form_id = $_POST['form_id'];
            $formfield = FrmFieldsHelper::get_form_fields($form_id, '');
            $html .='<select name="esig_formidableform_field_id" class="chosen-select" style="width:250px;">';
            foreach ($formfield as $field) {
                if ($field->type == 'captcha') {
                    continue;
                }

                $html .= '<option value= ' . $field->id . ' >' . $field->name . '</option>';
            }
            $html .='</select>';
            echo $html;
            die();
        }

        public function document_add_data($more_option_page) {
            $more_option_page .= $this->document_view->esig_formidableform_document_view();
            return $more_option_page;
        }

        public function render_shortcode_esigformidable($atts) {

            extract(shortcode_atts(array(
                'formid' => '',
                'field_id' => '', //foo is a default value
                            ), $atts, 'esigformidable'));

            if (!function_exists('WP_E_Sig'))
                return;
            $csum = isset($_GET['csum']) ? sanitize_text_field($_GET['csum']) : null;

            if (empty($csum)) {
                $documentId = get_option('esig_global_document_id');
            } else {
                $documentId = WP_E_Sig()->document->document_id_by_csum($csum);
            }

            if (empty($formid)) {
                return;
            }

            $underLineData = WP_E_Sig()->meta->get($documentId, 'esig_formidable_underlinedata');


            $formidableValue = self::generate_value($documentId, $formid, $field_id);



            if (!$formidableValue) {
                return;
            }
            $get_type = FrmField::get_type($field_id);

            return self::display_value($formidableValue, $underLineData);
        }

        final function esig_almost_done_formidableform_settings() {

            if (!function_exists('WP_E_Sig'))
                return;

            // getting sad document id 
            $sad_document_id = isset($_GET['doc_preview_id']) ? $_GET['doc_preview_id'] : null;
            if (!$sad_document_id) {
                return;
            }
            // creating esignature api here 
            $documents = WP_E_Sig()->document->getDocument($sad_document_id);
            $document_content = $documents->document_content;
            $document_raw = WP_E_Sig()->signature->decrypt(ENCRYPTION_KEY, $document_content);
            if (has_shortcode($document_raw, 'esigformidable')) {
                preg_match_all('/' . get_shortcode_regex() . '/s', $document_raw, $matches, PREG_SET_ORDER);
                $esigformidable_shortcode = '';
                foreach ($matches as $match) {
                    if (in_array('esigformidable', $match)) {
                        $esigformidable_shortcode = $match[0];
                    }
                }
                $atts = shortcode_parse_atts($esigformidable_shortcode);
                extract(shortcode_atts(array(
                    'formid' => '',
                    'field_name' => '',
                                ), $atts, 'esigformidable'));
                $data = array("formid" => $formid);
                $display_notice = dirname(__FILE__) . '/views/alert-almost-done.php';
                WP_E_Sig()->view->renderPartial('', $data, true, '', $display_notice);
            }
        }

        public function register_actions($actions) {
            $actions['e-signature'] = 'FrmDefEsigAction';
            include_once( plugin_dir_path(__FILE__) . 'includes/esig-form-action-class.php' );
            return $actions;
        }

        public static function esig_formidable_after_entry($action, $entry, $form) {

            $formId = $form->id;
            $entryId = $entry->id;
            $formAction = $action->post_content;
            $actionOption = $form->options;

            $sad = new esig_sad_document();
            $signingLogic = esigget('signing_logic', $formAction);
            $sadPageId = esigget('select_sad', $formAction);
            $documentId = $sad->get_sad_id($sadPageId);
            $signerEmail = esigget('signer_email', $formAction);
            $signerName = esigget('signer_name', $formAction);
            $reminderSet = esigget('enable_signing_reminder_email', $formAction);
            WP_E_Sig()->meta->add($documentId, 'esig_formidable_underlinedata', esigget('underline_data', $formAction));
            self::save_entry_value($documentId, $entry);
            if ($reminderSet == '1') {

                $reminderEmail = $formAction['reminder_email'];
                $firstReminderSend = $formAction['first_reminder_send'];
                $expireReminder = $formAction['expire_reminder'];
                $esigFormidableReminderSettings = array(
                    "esig_reminder_for" => $reminderEmail,
                    "esig_reminder_repeat" => $firstReminderSend,
                    "esig_reminder_expire" => $expireReminder,
                );

                WP_E_Sig()->meta->add($documentId, "esig_reminder_settings_", json_encode($esigFormidableReminderSettings));
                WP_E_Sig()->meta->add($documentId, "esig_reminder_send_", "1");
            }

            //  $email = $actionOption['signer_email'];
            // $name = $actionOption['signer_name'];
            $signerName = self::get_submission_value($documentId, $signerName);
            $signerEmail = self::get_submission_value($documentId, $signerEmail);
            // if not email address 
            if (!is_email($signerEmail)) {
                return;
            }

            //sending email invitation / redirecting .
            $result = self::esig_invite_document($documentId, $signerEmail, $signerName, $formId, $entryId, $signingLogic);
        }

        public static function esig_invite_document($oldDocId, $signerEmail, $signerName, $formId, $entryId, $signingLogic) {

            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();

            global $wpdb;



            /* make it a basic document and then send to sign */
            $old_doc = WP_E_Sig()->document->getDocument($oldDocId);

            $docTable = $wpdb->prefix . 'esign_documents';


            // Copy the document
            $docId = WP_E_Sig()->document->copy($oldDocId);

            WP_E_Sig()->meta->add($docId, 'esig_formidable_form_id', $formId);
            WP_E_Sig()->meta->add($docId, 'esig_formidable_entry_id', $entryId);
            WP_E_Sig()->document->saveFormIntegration($docId, 'formidable');
            // set document timezone
            $esigCommon = new WP_E_Common();
            $esigCommon->set_document_timezone($docId);
            // Create the user=
            $recipient = array(
                "user_email" => $signerEmail,
                "first_name" => $signerName,
                "document_id" => $docId,
                "wp_user_id" => '',
                "user_title" => '',
                "last_name" => ''
            );

            $recipient['id'] = WP_E_Sig()->user->insert($recipient);

            $documentType = 'normal';
            $documentStatus = 'awaiting';
            $docTitle = $old_doc->document_title . ' - ' . $signerName;
            // Update the doc title
            $affected = $wpdb->query($wpdb->prepare(
                            "UPDATE " . $docTable . " SET document_title = '%s',document_type ='%s' , document_status='%s' where document_id = %d", $docTitle, $documentType, $documentStatus, $docId));

            $doc = WP_E_Sig()->document->getDocument($docId);

            // trigger an action after document save .
            do_action('esig_sad_document_invite_send', array(
                'document' => $doc,
                'old_doc_id' => $oldDocId,
            ));


            // Get Owner
            $owner = WP_E_Sig()->user->getUserByID($doc->user_id);


            // Create the invitation?
            $invitation = array(
                "recipient_id" => $recipient['id'],
                "recipient_email" => $recipient['user_email'],
                "recipient_name" => $recipient['first_name'],
                "document_id" => $docId,
                "document_title" => $doc->document_title,
                "sender_name" => $owner->first_name . ' ' . $owner->last_name,
                "sender_email" => $owner->user_email,
                "sender_id" => 'stand alone',
                "document_checksum" => $doc->document_checksum,
                "sad_doc_id" => $oldDocId,
            );

            $inviteController = new WP_E_invitationsController();

            if ($signingLogic == "email") {

                if ($inviteController->saveThenSend($invitation, $doc)) {

                    return true;
                }
            } elseif ($signingLogic == "redirect") {


                $invitationId = $inviteController->save($invitation);
                $inviteHash = WP_E_Sig()->invite->getInviteHash($invitationId);
                ESIG_FORMIDABLEFORM_SETTING::save_esig_ff_meta($inviteHash, "signed", "no");


                // if (!get_transient("esig-ff-redirect-" . esig_get_ip())) {
                // set_transient("esig-ff-redirect-" . esig_get_ip(), WP_E_Sig()->invite->get_invite_url($inviteHash, $doc->document_checksum), 120);
                // }
                return true;

                // wp_redirect(WP_E_Invite::get_invite_url($inviteHash, $doc->document_checksum));
                //exit;
            }
        }

        public function esig_formidableform_adminmenu() {
            add_submenu_page('formidable', __('E-signature', 'esig'), __('E-signature', 'esig'), 'read', 'esign-formidableform-about', array(&$this, 'formidableform_about_page'));
            if (!function_exists('WP_E_Sig')) {


                if (empty($GLOBALS['admin_page_hooks']['esign'])) {
                    add_menu_page('E-Signature', 'E-Signature', 'read', "esign", array(&$this, 'esig_core_page'), plugins_url('assets/images/pen_icon.svg', __FILE__));
                }

                add_submenu_page("esign", "Formidable Forms", "Formidable Forms", 'read', "esign-formidableform-about", array(&$this, 'formidableform_about_page'));


                return;
            }
        }

        final function show_sad_invite_link($show, $doc, $page_id) {
            if (!isset($doc->document_content)) {
                return $show;
            }
            $document_content = $doc->document_content;
            $document_raw = WP_E_Sig()->signature->decrypt(ENCRYPTION_KEY, $document_content);
            if (has_shortcode($document_raw, 'esigformidable')) {
                $show = false;
                return $show;
            }
            return $show;
        }

        final function show_invite_error($ret, $docId) {

            $doc = WP_E_Sig()->document->getDocument($docId);
            if (!isset($doc->document_content)) {
                return $show;
            }
            $document_content = $doc->document_content;
            $document_raw = WP_E_Sig()->signature->decrypt(ENCRYPTION_KEY, $document_content);

            if (has_shortcode($document_raw, 'esigformidable')) {

                $ret = true;
                return $ret;
            }
            return $ret;
        }

        public function formidableform_about_page() {
            include_once(dirname(__FILE__) . "/views/formidableform-esign-about.php");
        }
        
        public function esig_core_page() {
            include_once(dirname(__FILE__) . "/views/esig-core-about.php");
        }

        final function esig_formidableform_addon_requirement() {

            if (class_exists('FrmHooksController') && function_exists("WP_E_Sig") && class_exists('ESIG_SAD_Admin') && class_exists('ESIG_SIF_Admin'))
                return;
            include_once "views/alert-modal.php";
        }

        public function enqueue_admin_styles() {

            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-formidableform-about',
                'forms_page_esign-formidableform-about',
                'formidableform_page_esign-formidableform-about'
            );
            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/esig-formidableform-about.css', __FILE__), array());
            }
        }

        public function enqueue_admin_scripts() {
            $screen = get_current_screen();
            $admin_screens = array(
                'admin_page_esign-add-document',
                'admin_page_esign-edit-document',
                'e-signature_page_esign-view-document',
            );
            if (in_array($screen->id, $admin_screens)) {
                wp_enqueue_script('jquery');
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-add-formidableform.js', __FILE__), array('jquery', 'jquery-ui-dialog'), '0.1.0', true);
            }
            if ($screen->id != "plugins") {
                wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/esig-formidableform-control.js', __FILE__), array('jquery', 'jquery-ui-dialog'), '0.1.0', true);
            }
        }

        public function wpform_wpesignature_init_text_domain() {

            load_plugin_textdomain('formidableform-wpesignature', FALSE, FORMIDABLEFORM_WPESIGNATURE_PATH . 'languages');
        }

        /**
         * Return an instance of this class.
         * @since     0.1
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

    }

    

    

endif;

