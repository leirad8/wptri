<?php
/*
 * Move all custom code here, instead of requiring users to add to theme file
 * @since 4.5.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	die;
}

// Custom url for post
add_filter( 'pt_cv_field_href', 'cvp_cc_use_custom_url', 100, 2 );
function cvp_cc_use_custom_url( $href, $post ) {
	// If have not executed filter in theme
	if ( !has_filter( 'pt_cv_field_href', 'my_field_href' ) && apply_filters( PT_CV_PREFIX_ . 'enable_custom_url', true ) ) {
		if ( !isset( $post->cvp_custom_url ) ) {
			$meta = get_post_meta( $post->ID );

			$custom_href = 0;
			# WordPress, ACF
			if ( !empty( $meta[ 'cv_custom_url' ][ 0 ] ) ) {
				$custom_href = $meta[ 'cv_custom_url' ][ 0 ];
			}
			# Types
			if ( !$custom_href && !empty( $meta[ 'wpcf-cv_custom_url' ][ 0 ] ) ) {
				$custom_href = $meta[ 'wpcf-cv_custom_url' ][ 0 ];
			}

			$post->cvp_custom_url = $custom_href;
		}

		if ( !empty( $post->cvp_custom_url ) ) {
			# Add site URL to relative value, for example: /page1
			if ( !filter_var( $post->cvp_custom_url, FILTER_VALIDATE_URL ) ) {
				$post->cvp_custom_url = get_site_url() . $post->cvp_custom_url;
			}

			$href = esc_url( apply_filters( PT_CV_PREFIX_ . 'custom_href_url', $post->cvp_custom_url ) );
		}
	}

	return $href;
}
