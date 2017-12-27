<?php

add_shortcode( 'pt_simple_search', 'cvp_simple_search' );
function cvp_simple_search( $atts ) {
	$atts = shortcode_atts( array(
		'search'		 => 'local',
		'fields'		 => 'title,content',
		'placeholder'	 => __( 'Type here', 'content-views-pro' ),
		), $atts );

	return sprintf( '<div class="%s"><input value="" type="search" placeholder="%s" data-search="%s" data-fields="%s"></div>', PT_CV_PREFIX . 'simple-search', esc_attr( $atts[ 'placeholder' ] ), esc_attr( $atts[ 'search' ] ), esc_attr( $atts[ 'fields' ] ) );
}
