<?php
class FrmDefEsigAction extends FrmFormAction{
        public function __construct() {
            
            $action_ops = array(
            'classes'   => 'icon icon-esignature',
            'active'    => true,
            'event'     => array( 'create' ),
            'limit'     => 99,
            'priority'  => 10,
            'ajax_load' => false,
		);
                    
                 $action_options = apply_filters('frm_esig_control_settings', $action_ops);
                 
                 wp_enqueue_style('esig-formidable-icon-css');
                 
                parent::__construct( 'esig', __( 'E-signature', 'formidable' ), $action_options );
       
	}
        
        public function form( $form_action, $args = array() ) {
                extract($args);
                $form =  $args['form'];
                $formId = $form->id;
                
            include( dirname(__FILE__) .'/esig_action_settings.php');
		
	}
        
        public function get_defaults() {
	    return array(
            'signer_name'   => '[signer_name]',
            'signer_email'  => '[signer_email]',
            'signing_logic'  => '',
            'select_sad' => '',
            'underline_data' => '[default-message]',
            'enable_signing_reminder_email' => '',
            'reminder_email' => '',
            'first_reminder_send' => '',
            'expire_reminder' => '',
	    );
	}
        
}