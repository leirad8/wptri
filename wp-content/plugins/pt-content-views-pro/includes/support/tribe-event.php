<?php
/**
 * @author PT Guy https://www.contentviewspro.com/
 * @since 4.2
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	die;
}

add_filter( PT_CV_PREFIX_ . 'ctf_intersect', 'tribe_cvp_filter_ctf_intersect' );
function tribe_cvp_filter_ctf_intersect( $args ) {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$args = false;
	}

	return $args;
}

add_filter( PT_CV_PREFIX_ . 'ctf_value', 'tribe_cvp_filter_ctf_value', 11, 3 );
function tribe_cvp_filter_ctf_value( $field_value, $key, $object ) {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$func = 0;
		if ( stripos( $key, '_venue' ) !== false ) {
			$func = 'tribe_get_';
		} elseif ( stripos( $key, '_organizer' ) !== false ) {
			$func = 'tribe_get_organizer_';
		}

		$field	 = str_ireplace( array( '_venue', '_organizer' ), '', $key );
		$field	 = strtolower( sanitize_key( $field ) );
		if ( $field && function_exists( "{$func}{$field}" ) ) {
			$field_value = @call_user_func( "{$func}{$field}", $object->ID );
		}
	}

	return $field_value;
}
