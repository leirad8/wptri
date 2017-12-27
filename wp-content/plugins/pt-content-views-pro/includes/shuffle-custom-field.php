<?php
/*
 * Enable to use Custom field in Shuffle bar
 * @since 4.5
 */

add_filter( PT_CV_PREFIX_ . 'shortcode_params', 'cvpsf_ctf_shortcode_params', 11 );
function cvpsf_ctf_shortcode_params( $args ) {
	$args[ 'shuffle_custom_field' ] = '';
	return $args;
}

// Able to show Shuffle bar of custom fields (without select any taxonomy)
add_filter( PT_CV_PREFIX_ . 'shuffle_require_taxonomy', 'cvpsf_ctf_wo_taxonomy', 11 );
function cvpsf_ctf_wo_taxonomy( $args ) {
	$sc_params = PT_CV_Functions::get_global_variable( 'shortcode_params' );
	if ( !empty( $sc_params[ 'shuffle_custom_field' ] ) ) {
		$args = false;
	}

	return $args;
}

add_action( PT_CV_PREFIX_ . 'custom_view_parameters', 'cvpsf_ctf_init' );
function cvpsf_ctf_init() {
	new CVP_Custom_SF();
}

class CVP_Custom_SF {

	protected $custom_fields = null;

	public function __construct() {
		$this->get_custom_fields();
		if ( $this->custom_fields ) {
			add_filter( PT_CV_PREFIX_ . 'shuffle_filter_extra', array( $this, 'shuffle_add_ctf' ), 100, 1 );
			add_filter( PT_CV_PREFIX_ . 'post_groups', array( $this, 'post_add_ctf_data' ), 100, 2 );
		}
	}

	// Get custom fields to show in shuffle
	function get_custom_fields() {
		$sc_params = PT_CV_Functions::get_global_variable( 'shortcode_params' );
		if ( !empty( $sc_params[ 'shuffle_custom_field' ] ) ) {
			$val = $sc_params[ 'shuffle_custom_field' ];
			if ( $val === 'GET_CURRENT' ) {
				// Get selected Custom Fields in View
				$ctf_info = PT_CV_Functions::get_global_variable( 'custom_field_1' );
				if ( !$ctf_info ) {
					$ctf_info = PT_CV_Functions::settings_values_by_prefix( PT_CV_PREFIX . 'custom-fields-' );
					PT_CV_Functions::set_global_variable( 'custom_field_1', $ctf_info );
				}

				$val = $ctf_info[ 'list' ];
			} else {
				// Get from parameter value
				$val = array_map( 'trim', explode( ',', $val ) );
			}

			$this->custom_fields = $val;
		}
	}

	// Add custom field to shuffle bar
	function shuffle_add_ctf( $args ) {
		foreach ( $this->custom_fields as $idx => $field ) {
			$field_vals	 = $this->get_ctf_values( $field );
			$options	 = array();

			foreach ( $field_vals as $val ) {
				$options[ $this->shuffle_value( $val, $field ) ] = $val;
			}
			$args[ "ctf{$idx}" ] = $options;
		}

		return $args;
	}

	// Add custom field to post data
	function post_add_ctf_data( $args, $post_id ) {
		foreach ( $this->custom_fields as $field ) {
			$field_vals = $this->get_ctf_values( $field, $post_id );
			foreach ( $field_vals as $val ) {
				$args[] = $this->shuffle_value( $val, $field );
			}
		}

		return $args;
	}

	// Identify each custom field & unique value
	function shuffle_value( $val, $prefix ) {
		return sanitize_title( 'ctf-' . $prefix . $val );
	}

	// Get all values of custom field (ACF, Pods, WP)
	function get_ctf_values( $field_name, $post_id = 0 ) {
		global $wpdb;
		if ( !$post_id ) {
			$acf_values = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key=%s", $field_name ) );
		} else {
			$acf_values = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key=%s AND post_id=%s", $field_name, $post_id ) );
		}

		$result = array();
		foreach ( $acf_values as $value ) {
			$val		 = maybe_unserialize( $value->meta_value );
			$result[]	 = is_array( $val ) ? $val[ 0 ] : $val;
		}

		return $result;
	}

}
