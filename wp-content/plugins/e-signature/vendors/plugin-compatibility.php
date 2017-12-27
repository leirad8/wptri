<?php

/**
 *  excldue css handler 
 *  plugin compatibility check with others 
 */
add_action('admin_init', 'esig_dequeue_other_plugin', 20);

function esig_dequeue_other_plugin() {
    $page = (isset($_GET['page'])) ? $_GET['page'] : null;
    if (!empty($page)) {
        if (preg_match('/^esign/', $page)) {
            wp_dequeue_style('jquery-ui-lightness');
            remove_all_actions("admin_notices");
        }
    }
}

function esig_older_version($document_id) {
    $document = new WP_E_Document();

    $upload_event = $document->get_upload_event($document_id);

    if ($upload_event) {
        return true;
    } else {
        return false;
    }
}

function esig_remove_template_include_filter() {

    $setting = new WP_E_Setting();
    $default_display_page = $setting->get_generic('default_display_page');

    $current_page_id = get_queried_object_id();

    if (class_exists('esig_sad_document')) {
        $sad = new esig_sad_document();

        $sad_doc_id = $sad->get_sad_id($current_page_id);
        if ($sad_doc_id) {
            $default_display_page = $current_page_id;
        }
    }


    if (!is_page($default_display_page))
        return;

    if (!has_esig_shortcode($default_display_page))
        return;

    $hook_name = 'template_include';
    global $wp_filter;

    if (array_key_exists($hook_name, $wp_filter)) {
        foreach ($wp_filter[$hook_name] as $priority => $filter) {
            foreach ($filter as $identifier => $function) {

                if (is_array($function)) {
                    remove_filter(
                            $hook_name, array($function['function'][0], $function['function'][1]), $priority
                    );
                }
            }
        }
    }
    // if nexus theme remove nexus template include filter. 
    if (defined('NXS_FRAMEWORKLOADED')) {
        remove_filter('template_include', 'nxs_template_include', 9999);
    }

    if (class_exists('BWP_MINIFY')) {
        add_filter('bwp_minify_is_loadable', 'bwp_loads', -100, 1);
    }

    add_filter('template_include', array('Esign_core_load', 'documentTemplateHook'), -29);
}

add_action("template_redirect", "esig_remove_template_include_filter", -100);

function files_to_delete() {

    Esig_Addons::esig_create_plugin_files();
}

// delete backward comppitable files 
add_action("admin_init", "files_to_delete", 15);

function esig_update_notice() {


    if (Esig_Addons::is_updates_available()) {

        echo "<link rel='stylesheet' id='open-sans-css'  href='" . ESIGN_ASSETS_DIR_URI . "/css/style.css' type='text/css' media='all' />";

        echo '<div class="error">
                
        <div style="width:80%;display:inline-block;"><span class="icon-esig-alert"></span> <h4>' . __('UPDATE REQUIRED ASAP: WP E-Signature add-ons require a MAJOR critical update.  <a href="https://www.approveme.com/wordpress-contract-plugin/wp-online-contract-e-signature/">Read all about it here</a>', 'esig') . '</h4></div> <div style="width:18%;display:inline-block;text-align:right;" ><a href="' . admin_url() . 'admin.php?page=esign-addons' . '"  class="esig-alert-btn"> Update Now </a></div>
    </div>';
    }
}

add_action('admin_notices', 'esig_update_notice');


if (!function_exists('Esig_user_alert')) {

    function Esig_user_alert($current_user, $userids) {

        foreach ($userids as $userid) {

            if (WP_E_Sig()->user->getUserByWPID($userid)) {
                echo "<link rel='stylesheet' id='open-sans-css'  href='" . ESIGN_ASSETS_DIR_URI . "/css/style.css' type='text/css' media='all' />";
                $userdata = get_userdata($userid);
                $super_admin = WP_E_Sig()->user->esig_get_super_admin_id();
                if ($super_admin == $userid) {
                    echo '<div class="esig-error">
                       <div style="margin-bottom:10px;"> <h1> Urgent! WP E-Signature could stop working </h1></div>
        <div style="esig-error-left"><span class="icon-esig-alert"></span> </div> 
        <div style="esig-error-right" >Warning: You are attempting to delete ' . $userdata->user_login . ' 
        which 
        is currently the “E-SIGN Admin” for WP E-Signature. If you delete this user before assigning a new E-Sign admin” you will be 
        locked out and will require techincal assitance to reset the plugin. <strong>Please login as ' . $userdata->user_login . ' and choose 
        a NEW “E-Sign admin” user BEFORE deleting this account.</strong></div>
                     </div>';
                } elseif (WP_E_Sig()->document->total_byuser($userid) > 0) {
                    echo '<div class="esig-error">
                       <div style="margin-bottom:10px;"> <h1> Urgent! WP E-Signature could stop working </h1></div>
        <div class="esig-error-left"><span class="icon-esig-alert"></span> </div> 
        <div class="esig-error-right" >
        Warning: You are attempting to delete <strong>' . $userdata->user_login . '</strong> which is an “E-SIGN Document Sender" for WP E-Signature. 
        If you delete this user they will no longer have access to their sent and signed documents created with WP E-Signature.
        Are you sure you would like to proceed?</div>
                     </div>';
                }
            }
        }
    }

    add_action("delete_user_form", "Esig_user_alert", 10, 2);
}

add_filter('wp_die_handler', 'esig_wp_core_die', 10, 1);

function esig_wp_core_die($action) {

    return 'esig_rewrite_wp_die';
}

function esig_rewrite_wp_die($message, $title, $args) {
    
    if (is_wp_error($message)) {
        _default_wp_die_handler($message, $title, $args);
    }
    if (!is_string($message)) {
        _default_wp_die_handler($message, $title, $args);
    }

    if (strpos($message, 'esign-') !== false) {

        echo "<link rel='stylesheet' id='esig-style-css'  href='" . plugins_url('assets/css/style.css?ver=1.0.9', dirname(__FILE__)) . "' type='text/css' media='screen' />";

        $admin_user_id = WP_E_Sig()->user->esig_get_super_admin_id();

        $user_details = get_userdata($admin_user_id);

        $esig_admin = '<div class="esig-updated" style="padding: 11px;width: 515px;margin-top: 17px;">' . __('Super admin is', 'esig') . ' : <span>' . esc_html($user_details->display_name) . '-<a href="mailto:' . $user_details->user_email . '">' . __('Send an email', 'esig') . '</a></span></div>';

        // Currently only administrators have access to this plugin
        $settings = new WP_E_SettingsController();

        $data = array(
            "feature" => __('Multiple Users', 'esig'),
            "esig_user_role" => $esig_admin,
        );


        echo '<body style="background:#f1f1f1">';
        $invite_message = $settings->view->renderPartial('upgrade-roles', $data, true, 'settings');
        echo '</body>';
        die();
    }
    _default_wp_die_handler($message, $title, $args);
}

function bwp_loads($loads) {

    return false;
}


?>