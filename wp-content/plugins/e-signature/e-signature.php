<?php
/*
  Plugin Name: WP E-Signature
  Description: Legally sign and collect signatures on documents, contracts, proposals, estimates and more using WP E-Signature.
  Version: 1.5.0.3
 
  Author: Approve Me
  Author URI: https://www.approveme.com
  Contributors: Kevin Michael Gray, Micah Blu, Michael Medaglia, Abu Shoaib, Earl Red, Pippin Williamson
  Text Domain: esig
  Domain Path:       /languages
  License/Terms and Conditions: https://www.approveme.com/terms-conditions/
  License/Terms of Use: https://www.approveme.com/terms-of-use/
  Privacy Policy: https://www.approveme.com/privacy-policy/
 */


if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists('ESIG_License'))
    include( dirname(__FILE__) . '/vendors/WP_E_License_Handler.php' );


$license = new ESIG_License(__FILE__, __('WP E-Signature', 'esig'), '1.5.0.3', __('Approve Me', 'esig'));


if (!class_exists('WP_E_Digital_Signature')) :

    final class WP_E_Digital_Signature {

        protected static $_instance = null;

        /**
         * Creates singleton instance of the class
         * 
         * @since 1.0.1
         * @param null
         * @return void
         */
        public static function instance() {

            if (is_null(self::$_instance)) {
                self::$_instance = new self();
                self::$_instance->setup_constants();
                self::$_instance->includes();
                // defining other variable . 
                self::$_instance->view = new WP_E_View();
                self::$_instance->invite = new WP_E_Invite;
                self::$_instance->document = new WP_E_Document;
                self::$_instance->user = new WP_E_User;
                self::$_instance->signer = new WP_E_Signer();
                self::$_instance->setting = new WP_E_Setting;
                self::$_instance->validation = new WP_E_Validation();
                self::$_instance->notice = new WP_E_Notice();
                self::$_instance->email = new WP_E_Email();
                self::$_instance->meta = new WP_E_Meta;
                self::$_instance->common = new WP_E_Common();
                self::$_instance->signature = new WP_E_Signature;
                // @depricated in 1.4.0 
                self::$_instance->shortcode = new WP_E_Shortcode();
                
            }

            return self::$_instance;
        }

        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 1.3.2
         * @access protected
         * @return void
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'esig'), '1.6');
        }

        /**
         * Disable unserializing of the class
         *
         * @since 1.3.2
         * @access protected
         * @return void
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'esig'), '1.6');
        }

        /**
         * Include required files
         *
         * @access private
         * @since 1.4
         * @return void
         */
        private function includes() {
            require_once ESIGN_PLUGIN_PATH . '/includes/Query.php';
            include_once ESIGN_PLUGIN_PATH . "/lib/autoload.php";
            require_once ESIGN_PLUGIN_PATH . '/includes/Esign_core_load.php';
           

            require_once ESIGN_PLUGIN_PATH . '/includes/Esign_actions.php';
            // laods some other files . 
            include (dirname(__FILE__) . '/includes/Esign-add-on.php' ); 
            include( dirname(__FILE__) . '/vendors/core-load.php');
            include( dirname(__FILE__) . '/vendors/common-function.php');
            include( dirname(__FILE__) . '/includes/esig-core-function.php');
            include( dirname(__FILE__) . '/vendors/plugin-compatibility.php');
            include( dirname(__FILE__) . '/vendors/WP_E_Signature_Business_Updater.php');
           
        }

        /**
         * Setup plugin constants
         *
         * @access private
         * @since 1.3.5
         * @return void
         */
        private function setup_constants() {
            //prevent header sent. 
            ob_start();
            // Establish OS dependant Directory Separator
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                define('DS', "\\");
            else
                define('DS', '/');

            // esig plugin directory path 
            if (!defined('ESIGN_PLUGIN_PATH'))
                define('ESIGN_PLUGIN_PATH', dirname(__FILE__));

            // esig plugin directory file path 
            if (!defined('ESIGN_PLUGIN_FILE'))
                    define('ESIGN_PLUGIN_FILE', __FILE__);
            
            // esig plugin directory file path 
            if (!defined('ESIGN_PLUGIN_BASENAME'))
                    define('ESIGN_PLUGIN_BASENAME', plugin_basename(__FILE__));
            //esig venders directory path 
            if (!defined('ESIGN_VENDORS_PATH'))
                define('ESIGN_VENDORS_PATH', ESIGN_PLUGIN_PATH . DS . 'vendors' . DS);

            //esig template directory path 
            if (!defined('ESIGN_TEMPLATES_PATH'))
                define('ESIGN_TEMPLATES_PATH', ESIGN_PLUGIN_PATH . DS . 'page-template' . DS);

            // esig signatures directory path 
            if (!defined('ESIGN_SIGNATURES_PATH'))
                define('ESIGN_SIGNATURES_PATH', ESIGN_PLUGIN_PATH . DS . 'e-signature-files'); // SECURITY option to be unique/random/custom


                
//esig plugin directory url 
            if (!defined('ESIGN_DIRECTORY_URI'))
                define('ESIGN_DIRECTORY_URI', plugins_url("/", __FILE__));

            // esig asset directory url 
            if (!defined('ESIGN_ASSETS_DIR_URI'))
                define('ESIGN_ASSETS_DIR_URI', plugins_url('assets', __FILE__));

            // define encription key 
            if (!defined('ENCRYPTION_KEY'))
                define("ENCRYPTION_KEY", "!@#$%^&*");

            // esig log directory full path 
            if (!defined('ESIG_LOG_DIR')) {
                $upload_dir = wp_upload_dir();
                define('ESIG_LOG_DIR', $upload_dir['basedir'] . '/esig-logs/');
            }
        }

    }

    endif; // Ends if class exists

/**
 * @deprecated since 1.3.5
 * @return type
 */
function WP_E_Sig() {
    return WP_E_Digital_Signature::instance();
}

// run esignature 
WP_E_Sig();
