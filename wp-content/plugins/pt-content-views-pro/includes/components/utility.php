<?php
/*
 * List of utility functions
 */
/**
 * Get list of Views
 * @since 4.6.0
 */
function cvp_get_view_list( $default = false ) {
	$result = array( '' => $default ? $default : __( '(Select View)', 'content-views-pro' ) );

	$query1 = new WP_Query( array(
		'post_type'		 => PT_CV_POST_TYPE,
		'posts_per_page' => -1
		) );

	if ( $query1->have_posts() ) {
		while ( $query1->have_posts() ) {
			$query1->the_post();

			$view_id = get_post_meta( get_the_ID(), PT_CV_META_ID, true );
			if ( $view_id ) {
				$result[ $view_id ] = get_the_title();
			}
		}
	}

	wp_reset_postdata();

	return $result;
}

/**
 * Get selected terms of the View
 * Copied from view_get_advanced_settings()
 *
 * @since 4.6.0
 *
 * @param array $view_settings
 * @return array
 */
function cvp_get_selected_terms( $view_settings ) {
	$taxonomies		 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'taxonomy', $view_settings );
	$tax_settings	 = array();
	foreach ( (array) $taxonomies as $taxonomy ) {
		$terms = (array) PT_CV_Functions::setting_value( PT_CV_PREFIX . $taxonomy . '-terms', $view_settings );
		if ( $terms ) {
			$operator = PT_CV_Functions::setting_value( PT_CV_PREFIX . $taxonomy . '-operator', $view_settings, 'IN' );
			if ( $operator === 'AND' && count( $terms ) == 1 ) {
				$operator = 'IN';
			}

			$tax_settings[] = array(
				'taxonomy'			 => $taxonomy,
				'field'				 => 'slug',
				'terms'				 => $terms,
				'operator'			 => $operator,
				/**
				 * @since 1.7.2
				 * Bug: "No post found" when one of selected terms is hierarchical & operator is AND
				 */
				'include_children'	 => apply_filters( PT_CV_PREFIX_ . 'include_children', $operator == 'AND' ? false : true  )
			);
		}
	}

	if ( count( $tax_settings ) > 1 ) {
		$tax_settings[ 'relation' ] = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'taxonomy-relation', $view_settings, 'AND' );
	}

	return apply_filters( PT_CV_PREFIX_ . 'taxonomy_setting', $tax_settings );
}

/**
 * Get term meta
 * @since 4.6.0
 */
function cvp_get_term_meta( $term_id, $key, $single = true ) {
	return function_exists( 'get_term_meta' ) ? get_term_meta( $term_id, $key, $single ) : get_metadata( 'cvpro_term_meta', $term_id, $key, $single );
}

/**
 * Update term meta
 * @since 4.6.0
 */
function cvp_update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
	return function_exists( 'update_term_meta' ) ? update_term_meta( $term_id, $meta_key, $meta_value, $prev_value ) : update_metadata( 'cvpro_term_meta', $term_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Get current post ID
 *
 * @param string $return id, object
 * @return int
 */
function get_current_post_across_pagination( $return = 'id' ) {
	if ( defined( 'PT_CV_DOING_PREVIEW' ) ) {
		return 0;
	}

	$current_post = 0;

	global $post;
	if ( !empty( $post->ID ) ) {
		$current_post = ($return == 'id') ? $post->ID : $post;
	}

	if ( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'enable-pagination' ) && PT_CV_Functions::setting_value( PT_CV_PREFIX . 'pagination-type' ) === 'ajax' ) {
		global $pt_cv_id;
		$transient = 'cvp_current_post_' . $pt_cv_id;

		if ( PT_CV_Functions::get_global_variable( 'current_page' ) === 1 ) {
			set_transient( $transient, $current_post, 30 * MINUTE_IN_SECONDS );
		} else {
			$current_post = get_transient( $transient );
		}
	}

	return $current_post;
}
