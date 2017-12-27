<?php
/**
 * Fix FB share wrong image
 * @since 3.9.4
 */
add_action( 'wp_head', 'cvp_troubleshoot_fb_share_wrong_img', 100 );
function cvp_troubleshoot_fb_share_wrong_img() {
	$fix_fb_share = PT_CV_Functions::get_option_value( 'fb_share_wrong_image' );
	if ( $fix_fb_share ) {
		global $post;
		$attachment_url = '';
		if ( is_singular() ) {
			$attachment_id	 = is_attachment() ? $post->ID : get_post_thumbnail_id( $post->ID );
			$attachment_url	 = wp_get_attachment_url( $attachment_id );

			if ( empty( $attachment_url ) ) {
				$attachment_url = PT_CV_Hooks_Pro::get_inside_image( $post, 'full', $post->post_content );
			}
		}

		if ( $attachment_url ) {
			printf( '<meta property="og:image" content="%s"/>', esc_url( $attachment_url ) );
		}
	}
}

/**
 * When Relevanssi plugin enabled:
 * Fix: Search by multiple keywords doesn't work
 */
add_filter( 'relevanssi_prevent_default_request', 'cvp_relevanssi_prevent_default_request', 100, 2 );
function cvp_relevanssi_prevent_default_request( $prevent, $query ) {
	if ( isset( $query->query[ 'cv_multi_keywords' ] ) || $query->get( 'by_contentviews' ) ) {
		$prevent = false;
	}

	return $prevent;
}

add_action( PT_CV_PREFIX_ . 'before_query', 'cvp_troubleshoot_action_before_query' );
function cvp_troubleshoot_action_before_query() {
	/* Fix: invalid output because of query was modified by plugin "Woocommerce Exclude Categories PRO"
	 * @since 4.2
	 */
	if ( function_exists( 'wctm_pre_get_posts_query' ) ) {
		remove_action( 'pre_get_posts', 'wctm_pre_get_posts_query' );
	}
}

add_action( PT_CV_PREFIX_ . 'do_replace_layout', 'cvp_troubleshoot_do_replace_layout' );
function cvp_troubleshoot_do_replace_layout() {
	/** Fix: SearchWP can't apply its order when use CVP to replace search results
	 * @since 4.2
	 */
	add_filter( 'searchwp_outside_main_query', '__return_true' );

	/**
	 * Fix conflicts with Relevanssi plugin:
	 * - No posts found when replacing layout in Search results page
	 */
	remove_filter( 'posts_request', 'relevanssi_prevent_default_request', 10 );
}

/**
 * Fix conflict with Photon feature of Jetpack plugin: thumbnail is not visible in mobile devices, when enable lazyload
 * @since 4.3.1
 */
add_filter( 'jetpack_photon_skip_for_url', 'cvp_jetpack_photon_skip_for_url', 100, 4 );
function cvp_jetpack_photon_skip_for_url( $skip, $image_url, $args, $scheme ) {
	if ( strpos( $image_url, 'lazy_image.png' ) !== false ) {
		$skip = true;
	}

	return $skip;
}

/**
 * "Search Everything" plugin
 * Issue: Replace Layout in Taxonomy Archives doesn't work
 * @since 4.6.0
 */
add_action( 'pre_get_posts', 'cvp_comp_plugin_searcheverything' );
function cvp_comp_plugin_searcheverything( $query ) {
	if ( $query->get( 'by_contentviews' ) && class_exists( 'SearchEverything' ) && !empty( $GLOBALS[ 'wp_filter' ][ 'posts_search' ][ 10 ] ) ) {
		$arr = (array) $GLOBALS[ 'wp_filter' ][ 'posts_search' ][ 10 ];
		foreach ( array_keys( $arr ) as $filter ) {
			if ( strpos( $filter, 'se_search_where' ) !== false ) {
				remove_filter( 'posts_search', $filter );
			}
		}
	}

	return $query;
}

/**
 * Lazyload makes image of [gallery] not show
 */
add_filter( 'the_content', 'cvp_start_gallery_shortcode', 1 );
add_filter( 'the_content', 'cvp_end_gallery_shortcode', 9999 );
function cvp_start_gallery_shortcode( $content ) {
	global $cvp_prevent_lazyload;
	if ( preg_match( '/\[gallery[^\]]+\]/', $content ) ) {
		$cvp_prevent_lazyload = true;
	}

	return $content;
}

function cvp_end_gallery_shortcode( $content ) {
	global $cvp_prevent_lazyload;
	$cvp_prevent_lazyload = false;

	return $content;
}

/**
 * WP Rocket plugin
 * Fix broken Pinterest layout with WP Rocket lazyload
 * @since 4.7.0
 */
add_filter( 'wp_get_attachment_image_attributes', 'cvp_comp_plugin_wprocket', 999, 3 );
function cvp_comp_plugin_wprocket( $attr, $attachment = null, $size = null ) {
	global $cvp_process_settings;
	if ( $cvp_process_settings ) {
		$attr[ 'data-no-lazy' ] = 1;
	}

	return $attr;
}

/**
 * Get image inside post content, for Visual Composer plugin
 * @since 4.7.1
 */
add_filter( 'pt_cv_field_content_excerpt', 'cvp_comp_plugin_visual_composer_image_content', 100, 3 );
function cvp_comp_plugin_visual_composer_image_content( $args, $fargs, $post ) {
	// Run only when extracting image in content
	if ( empty( $fargs ) ) {
		if ( class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
			// Prevent lazyload from applying to VC image, which makes lazyload get & show its lazy image instead of VC image
			global $cvp_prevent_lazyload;
			$cvp_prevent_lazyload = true;

			WPBMap::addAllMappedShortcodes();
			$args = do_shortcode( $args );

			$cvp_prevent_lazyload = false;
		}
	}

	return $args;
}

/**
 * WPML 3.7.1 & WordPress 4.8.0: filter_taxonomy_setting doesn't work (the current language was injected to the query while getting term id from slug)
 */
function cvp_comp_plugin_wpml_wp480_taxonomy( $action = 'remove' ) {
	global $sitepress;
	if ( $sitepress && class_exists( 'SitePress' ) && !empty( $GLOBALS[ 'wp_filter' ][ 'terms_clauses' ] ) ) {
		$function = ($action == 'remove') ? 'remove_filter' : 'add_filter';

		foreach ( $GLOBALS[ 'wp_filter' ][ 'terms_clauses' ] as $priority => $value ) {
			foreach ( $value as $filter ) {
				if ( !empty( $filter[ 'function' ][ 0 ] ) && $filter[ 'function' ][ 0 ] instanceof SitePress ) {
					$function( 'terms_clauses', $filter[ 'function' ], $priority, $filter[ 'accepted_args' ] );
				}
			}
		}
	}
}

function cvp_comp_get_term_by( $field, $slug, $taxonomy ) {
	cvp_comp_plugin_wpml_wp480_taxonomy( 'remove' );

	$result = get_term_by( $field, $slug, $taxonomy );

	cvp_comp_plugin_wpml_wp480_taxonomy( 'add' );

	return $result;
}

/**
 * Woocommerce double read-more buttons, if product is not purchasable or out of stock
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'cvp_comp_plugin_woocommerce_double_readmore', 999, 2 );
function cvp_comp_plugin_woocommerce_double_readmore( $link, $product ) {
	global $cvp_process_settings;
	if ( $cvp_process_settings ) {
		if ( strpos( $link, __( 'Read more', 'woocommerce' ) ) !== false ) {
			$dargs = PT_CV_Functions::get_global_variable( 'dargs' );
			// Hide Woocommerce readmore, if enabled CVPRO readmore
			if ( !empty( $dargs[ 'field-settings' ][ 'content' ] ) && $dargs[ 'field-settings' ][ 'content' ][ 'show' ] === 'excerpt' && isset( $dargs[ 'field-settings' ][ 'content' ][ 'readmore' ] ) ) {
				$link = '';
			}
		}
	}

	return $link;
}
