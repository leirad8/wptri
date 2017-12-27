<?php

class CVP_Replace_Layout {

	private static $instance;
	private $origin_query;
	private $which_page;
	private $which_view			 = false;
	private $show_heading		 = false;
	private $full_width			 = false;
	private $sort_by			 = false;
	private $display_comments	 = false;
	private $enable_pagination	 = false;
	private $done				 = false;
	private $container_class	 = CVP_REPLAYOUT;
	private $extra_class;

	public static function get_instance() {
		if ( !CVP_Replace_Layout::$instance ) {
			CVP_Replace_Layout::$instance = new CVP_Replace_Layout();
		}

		return CVP_Replace_Layout::$instance;
	}

	public function __construct() {
		if ( !is_admin() ) {
			add_action( 'get_header', array( $this, 'hook_header' ) );
			add_action( 'loop_start', array( $this, 'start_buffer' ), 0 );
			add_action( 'loop_end', array( $this, 'do_replace' ), 0 );

			add_filter( PT_CV_PREFIX_ . 'terms_data_for_shuffle', array( $this, 'filter_terms_data_for_shuffle' ) );
		} else {
			// For updating
			add_action( 'load-edit-tags.php', array( 'CVP_Replace_Layout_Admin', 'admin_action_term' ) );
			// For showing
			add_action( 'load-term.php', array( 'CVP_Replace_Layout_Admin', 'admin_action_term' ) );
		}
	}

	function hook_header() {
		global $wp_query;
		$this->origin_query = $wp_query;

		$this->get_page( $wp_query );
		$this->get_view();
	}

	function get_page( $wp_query ) {
		$arr = array(
//			'is_single',
//			'is_preview',
//			'is_page',
//			'is_archive',
			'is_date',
			'is_year',
			'is_month',
			'is_day',
			'is_time',
			'is_author',
			'is_category',
			'is_tag',
			'is_tax',
			'is_search',
//			'is_feed',
//			'is_comment_feed',
//			'is_trackback',
			'is_home',
//			'is_404',
//			'is_embed',
//			'is_paged',
//			'is_admin',
//			'is_attachment',
			'is_singular',
//			'is_robots',
//			'is_posts_page',
			'is_post_type_archive',
		);
		foreach ( $arr as $which ) {
			if ( !empty( $wp_query->$which ) ) {
				$page = str_replace( 'is_', '', $which );

				switch ( $which ):
					case 'is_date':
					case 'is_year':
					case 'is_month':
					case 'is_day':
					case 'is_time':
						$page = 'time';
						break;

					case 'is_category':
						$page = "tax-$page";
						break;

					case 'is_tag':
						$page = "tax-post_tag";
						break;

					case 'is_tax':
						$detail	 = $wp_query->query_vars[ 'taxonomy' ];
						$page	 = "tax-$detail";
						break;

					case 'is_singular':
						if ( is_singular( 'post' ) ) {
							$detail = 'post';
						} elseif ( $wp_query->is_page ) {
							$detail = 'page';
						} else {
							$detail = $wp_query->query_vars[ 'post_type' ];
						}

						$page = "is_singular-$detail";
						break;

					case 'is_post_type_archive':
						$detail	 = $wp_query->query_vars[ 'post_type' ];
						$page	 = "post_type-$detail";
						break;
				endswitch;

				$this->which_page = $page;
				break;
			}
		}
	}

	function get_view() {
		if ( !$this->which_page ) {
			return;
		}

		$settings	 = get_option( CVP_REPLAYOUT );
		$page		 = $this->which_page;
		if ( !empty( $settings[ $page ][ 'rep_status' ] ) ) {
			if ( !empty( $settings[ $page ][ 'selected_view' ] ) ) {
				$this->which_view = $settings[ $page ][ 'selected_view' ];
			}
			$this->modify_view();

			if ( !empty( $settings[ $page ][ 'sort_by' ] ) ) {
				$this->sort_by = $settings[ $page ][ 'sort_by' ];
			}

			if ( !empty( $settings[ $page ][ 'show_comment' ] ) ) {
				$this->display_comments = $settings[ $page ][ 'show_comment' ];
			}
		}

		$this->show_heading	 = !empty( $settings[ '-1-show-heading' ][ 'rep_status' ] );
		$this->full_width	 = !empty( $settings[ '-1-full-width' ][ 'rep_status' ] );
	}

	function start_buffer( $query ) {
		if ( $this->is_right_place( $query ) ) {
			ob_start();
		}
	}

	function is_right_place( $query ) {
		if ( PT_CV_Functions_Pro::user_can_manage_view() ) {
			if ( defined( 'PT_CV_VIEW_OVERWRITE' ) && $query->is_main_query() && $this->which_view && current_filter() === 'loop_start' ) {
				printf( '<div class="alert" style="background: #FFEB3B;padding: 10px;">%s</div>', __( 'For Administrator only: You already replaced layout by using method', 'content-views-pro' ) . ' <code>PT_CV_Functions_Pro::view_overwrite_tpl</code>' );
			}
		}

		return !is_admin() && $query->is_main_query() && $this->which_view && !$this->done && !defined( 'PT_CV_VIEW_OVERWRITE' );
	}

	function do_replace( $query ) {
		if ( $this->is_right_place( $query ) ) {
			do_action( PT_CV_PREFIX_ . 'do_replace_layout' );

			add_filter( PT_CV_PREFIX_ . 'set_current_page', array( $this, 'set_page_from_url' ) );
			add_filter( PT_CV_PREFIX_ . 'query_parameters', array( $this, 'do_not_modify_offset' ) );
			add_action( PT_CV_PREFIX_ . 'finished_replace', array( $this, 'disable_existing_pagination' ) );

			$this->clean_old_html();
			$this->get_new_html();
			$this->finished();
		}
	}

	function clean_old_html() {
		$old_layout = ob_get_clean();

		if ( apply_filters( PT_CV_PREFIX_ . 'replace_use_old_class', true ) ) {
			# Extract class from theme, to maintain style
			$matches = array();
			preg_match( '/class="([^"]+)"/', $old_layout, $matches );
			if ( !empty( $matches[ 1 ] ) ) {
				$first_class		 = preg_replace( '/\d/', '0', $matches[ 1 ] );
				// Exclude some classes name
				$first_class		 = preg_replace( '/\s?[^\s]*(google|ad|nocontent)[^\s]*\s?/i', ' ', $first_class );
				$this->extra_class	 = preg_replace( '/\s+/', ' ', $first_class );
			}
		}
	}

	function get_new_html() {
		if ( !$this->which_view ) {
			return;
		}

		$view_id = $this->which_view;

		if ( apply_filters( PT_CV_PREFIX_ . 'replace_completely', false, $this->which_page ) ) {
			# Completely replace page layout by output of View
			$view_output = do_shortcode( "[pt_view id=$view_id]" );
		} else {
			$wp_query = $this->origin_query;
			$this->modify_query( $wp_query );

			$view_settings								 = PT_CV_Functions::view_get_settings( $view_id );
			$this->set_orderby( $view_id, $view_settings, $wp_query->query_vars );
			$view_settings[ PT_CV_PREFIX . 'rebuild' ]	 = $wp_query->query_vars;

			if ( !empty( $view_settings[ PT_CV_PREFIX . 'enable-pagination' ] ) ) {
				$view_settings[ PT_CV_PREFIX . 'limit' ] = '-1';
				$this->enable_pagination				 = true;
			}

			$view_html	 = PT_CV_Functions::view_process_settings( $view_id, $view_settings );
			$view_output = PT_CV_Functions::view_final_output( $view_html );
		}

		$this->modify_output( $view_output );

		$class	 = $this->container_class . ' ' . $this->extra_class . ($this->full_width ? ' cvp-full-width' : '');
		$html	 = "<div class='$class'>$view_output</div>";

		echo apply_filters( PT_CV_PREFIX_ . 'replace_output', $html );
	}

	/**
	 * Modify View of current page
	 * @since 4.6.0
	 */
	function modify_view() {
		// For tax page only
		if ( strpos( $this->which_page, 'tax-' ) === 0 ) {
			$term_id		 = get_queried_object_id();
			$selected_view	 = cvp_get_term_meta( $term_id, 'cvp_view', true );
			if ( $selected_view ) {
				$this->which_view = $selected_view;
			}
		}
	}

	/**
	 * Correct the query parameters
	 * @since 4.6.0
	 * @param type $query
	 */
	function modify_query( &$query ) {
		$query->query_vars[ 'post_status' ] = 'publish';

		if ( $this->which_page === 'is_singular-page' ) {
			$query->query_vars[ 'post_type' ] = 'page';
		}
	}

	/**
	 * Prepend/append more info to output
	 * since 4.6.0
	 */
	function modify_output( &$output ) {
		if ( $this->show_heading ) {
			$title = $this->get_the_archive_title();
			if ( $title ) {
				$output = sprintf( '<h1 class="page-title">%s</h1>', $title ) . $output;
			}
		}

		if ( $this->display_comments ) {
			ob_start();
			@comments_template();
			$output .= ob_get_clean();
		}
	}

	/**
	 * Apply order settings of View
	 * @since 4.3
	 */
	function set_orderby( $view_id, $settings, &$query_vars ) {
		if ( $this->sort_by ) {
			global $pt_cv_glb, $pt_cv_id;
			$tmp_glb = $pt_cv_glb;
			$tmp_id	 = $pt_cv_id;

			# Temporarily set
			$pt_cv_glb	 = array();
			$pt_cv_id	 = $view_id;
			PT_CV_Functions::set_global_variable( 'view_settings', $settings );

			$advanced_settings = (array) PT_CV_Functions::setting_value( PT_CV_PREFIX . 'advanced-settings', $settings );
			if ( in_array( 'order', $advanced_settings ) ) {
				$orderby		 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'orderby', $settings );
				$order			 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'order', $settings );
				$order_settings	 = apply_filters( PT_CV_PREFIX_ . 'order_setting', array(
					'orderby'	 => $orderby,
					'order'		 => $orderby ? $order : '',
					) );

				if ( !empty( $order_settings[ 'orderby' ] ) ) {
					$query_vars = array_merge( $query_vars, $order_settings );
				}
			}

			# Revert
			$pt_cv_glb	 = $tmp_glb;
			$pt_cv_id	 = $tmp_id;
		}

		CVP_Replace_Layout_Compatible::init( $query_vars );
	}

	/**
	 * Disable pagination of theme/another plugin follows the replacing View
	 * @since 4.7
	 */
	function disable_existing_pagination() {
		if ( $this->enable_pagination ) {
			global $wp_query;
			$wp_query->max_num_pages = 1;
		}
	}

	/**
	 * Show correct posts in pages of replaced WP page
	 * @since 4.7
	 * @param int $page
	 * @return int
	 */
	function set_page_from_url( $page ) {
		global $wp_query;
		if ( !empty( $wp_query->query_vars[ 'paged' ] ) ) {
			$page = intval( $wp_query->query_vars[ 'paged' ] );
		}

		return $page;
	}

	/**
	 * Show correct posts in pages of replaced WP page
	 * @since 4.7
	 * @param array $args
	 * @return array
	 */
	function do_not_modify_offset( $args ) {
		// If this View doesn't enable pagination, leave the offset to be set by WP
		if ( !$this->enable_pagination ) {
			unset( $args[ 'offset' ] );
		}

		return $args;
	}

	/**
	 * Retrieve terms info to support Shuffle Filter
	 * @since 4.6.0
	 * @param type $terms
	 * @return type
	 */
	function filter_terms_data_for_shuffle( $terms ) {
		global $wp_query;
		if ( $this->is_right_place( $wp_query ) ) {
			$view_settings	 = PT_CV_Functions::get_global_variable( 'view_settings' );
			$selected_terms	 = cvp_get_selected_terms( $view_settings );
			if ( $selected_terms ) {
				$terms = $selected_terms;
			}
		}

		return $terms;
	}

	/**
	 * Retrieve the archive title based on the queried object.
	 *
	 * @return string Archive title.
	 */
	function get_the_archive_title() {
		if ( is_category() ) {
			$title = sprintf( __( 'Category: %s' ), single_cat_title( '', false ) );
		} elseif ( is_tag() ) {
			$title = sprintf( __( 'Tag: %s' ), single_tag_title( '', false ) );
		} elseif ( is_author() ) {
			$title = sprintf( __( 'Author: %s' ), '<span class="vcard">' . get_the_author() . '</span>' );
		} elseif ( is_year() ) {
			$title = sprintf( __( 'Year: %s' ), get_the_date( _x( 'Y', 'yearly archives date format' ) ) );
		} elseif ( is_month() ) {
			$title = sprintf( __( 'Month: %s' ), get_the_date( _x( 'F Y', 'monthly archives date format' ) ) );
		} elseif ( is_day() ) {
			$title = sprintf( __( 'Day: %s' ), get_the_date( _x( 'F j, Y', 'daily archives date format' ) ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'post format archive title' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = sprintf( __( 'Archives: %s' ), post_type_archive_title( '', false ) );
		} elseif ( is_tax() ) {
			$tax	 = get_taxonomy( get_queried_object()->taxonomy );
			/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
			$title	 = sprintf( __( '%1$s: %2$s' ), $tax->labels->singular_name, single_term_title( '', false ) );
		} else {
			# Customized by CVP
			if ( is_search() ) {
				$text_domain = apply_filters( PT_CV_PREFIX_ . 'theme_text_domain', wp_get_theme()->get( 'TextDomain' ) );
				$title		 = sprintf( __( 'Search Results for: %s', $text_domain ), get_search_query() );
			} elseif ( is_home() || is_singular() ) {
				$title = '';
			} else {
				$title = __( 'Archives' );
			}
		}

		return apply_filters( 'get_the_archive_title', $title );
	}

	function finished() {
		$this->done			 = true;
		$this->which_view	 = null;

		do_action( PT_CV_PREFIX_ . 'finished_replace' );
	}

}

CVP_Replace_Layout::get_instance();

class CVP_Replace_Layout_Admin {

	static $term_field = 'cvp_view';

	/**
	 * Add setting to Admin term page, to set View for replacing layout
	 * @since 4.6.0
	 */
	public static function admin_action_term() {
		$replace_data	 = get_option( CVP_REPLAYOUT );
		$taxes			 = get_taxonomies( array( 'public' => true ) );

		foreach ( $taxes as $tax ) {
			if ( !empty( $replace_data[ "tax-$tax" ][ 'rep_status' ] ) ) {
				add_action( $tax . '_edit_form_fields', array( __CLASS__, 'custom_view_for_term' ), 999, 2 );
				add_action( 'edit_term', array( __CLASS__, 'save_view_for_term' ), 999, 3 );
			}
		}
	}

	/**
	 * Add View select box for term
	 *
	 * @param string $term
	 * @param string $taxonomy
	 */
	static function custom_view_for_term( $term, $taxonomy ) {
		$selected_view	 = cvp_get_term_meta( $term->term_id, self::$term_field, true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Content Views', 'content-views-pro' ); ?></label></th>
			<td>
				<select id="display_type" name="<?php echo self::$term_field; ?>" class="postform">
					<?php
					$views			 = cvp_get_view_list( sprintf( __( '(Use selected View in "%s" page)', 'content-views-pro' ), __( 'Replace Layout', 'content-views-pro' ) ) );
					foreach ( $views as $view_id => $title ) {
						printf( '<option value="%s" %s>%s</option>', esc_attr( $view_id ), selected( $selected_view, $view_id, false ), esc_html( $title ) );
					}
					?>
				</select>
				<p class="description">
					<?php _e( "Select the View to replace layout of this term's archive page", 'content-views-pro' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save View for term
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	static function save_view_for_term( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST[ self::$term_field ] ) ) {
			cvp_update_term_meta( $term_id, self::$term_field, esc_attr( $_POST[ self::$term_field ] ) );
		}
	}

}

class CVP_Replace_Layout_Compatible {

	static function init( &$args ) {
		self::_compatible_woocommerce_order( $args );
	}

	/**
	 * Woocommerce orderby doesn't work in Product Taxonomy page
	 *
	 * @since 4.7.2
	 * @param array $args
	 * @return array
	 */
	static function _compatible_woocommerce_order( &$args ) {
		if ( !empty( $args[ 'wc_query' ] ) && !empty( $_GET[ 'orderby' ] ) ) {
			$orderby = esc_sql( $_GET[ 'orderby' ] );

			switch ( $orderby ) {
				case 'price':
					$args[ 'meta_key' ]	 = '_price';
					$args[ 'orderby' ]	 = array(
						'meta_value_num' => 'ASC',
						'ID'			 => 'DESC',
					);

					break;

				case 'price-desc':
					$args[ 'meta_key' ]	 = '_price';
					$args[ 'orderby' ]	 = array(
						'meta_value_num' => 'DESC',
						'ID'			 => 'DESC',
					);

					break;

				case 'popularity':
					$args[ 'meta_key' ]	 = 'total_sales';
					$args[ 'orderby' ]	 = array(
						'meta_value_num' => 'DESC',
						'ID'			 => 'DESC',
					);

					break;
			}
		}
	}

}
