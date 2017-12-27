<?php
/**
 * HTML output, class, id generating
 *
 * @package   PT_Content_Views_Pro
 * @author    PT Guy <http://www.contentviewspro.com/>
 * @license   GPL-2.0+
 * @link      http://www.contentviewspro.com/
 * @copyright 2014 PT Guy
 */
if ( !class_exists( 'PT_CV_Html_Pro' ) ) {

	/**
	 * @name PT_CV_Html_Pro
	 * @todo related HTML functions: Define HTML layout, Set class name...
	 */
	class PT_CV_Html_Pro {

		/**
		 * Scripts for Preview & WP frontend
		 */
		static function frontend_scripts() {
			PT_CV_Asset::enqueue(
				'public-pro', 'script', array(
				'src'	 => plugins_url( 'public/assets/js/cvpro.min.js', PT_CV_FILE_PRO ),
				'deps'	 => array( 'jquery' ),
				'ver'	 => PT_CV_VERSION_PRO,
				)
			);
		}

		/**
		 * Styles for Preview & WP frontend
		 */
		static function frontend_styles() {
			PT_CV_Asset::enqueue(
				'public-pro', 'style', array(
				'src'	 => plugins_url( 'public/assets/css/' . ( function_exists( 'cv_is_damaged_style' ) && cv_is_damaged_style() ? 'cvpro.im.min.css' : 'cvpro.min.css'), PT_CV_FILE_PRO ),
				'ver'	 => PT_CV_VERSION_PRO,
				)
			);
		}

		static function _get_fields_wrapper( $when = 'padding' ) {
			$prefix		 = PT_CV_PREFIX;
			$view_type	 = PT_CV_Functions::get_global_variable( 'view_type' );
			$pin_mas	 = PT_CV_Functions_Pro::is_pin_mas();
			$col_layout	 = PT_CV_Functions_Pro::is_column_layout();

			$padding_selector = ".{$prefix}content-item";

			if ( PT_CV_Functions_Pro::animate_activated_content_hover() && $when == 'padding' ) {
				$padding_selector = ".{$prefix}mask";
			} else if ( $pin_mas ) {
				$padding_selector = ".{$prefix}pinmas";
			} else if ( $col_layout ) {
				$padding_selector = ".{$prefix}ifield";
			} else if ( $view_type === 'scrollable' ) {
				$padding_selector = ".{$prefix}carousel-caption";
			}

			return $padding_selector;
		}

		/**
		 * Generate style for view with view id and font settings
		 *
		 * @param string $view_id     The unique id of view
		 * @param array  $view_styles The style settings of this view
		 *
		 * @return string The css of this view
		 */
		static function view_styles( $view_styles ) {
			if ( !isset( $view_styles[ 'font' ] ) ) {
				return '';
			}

			// Output Css
			global $pt_cv_glb, $pt_cv_id;
			$view_type	 = PT_CV_Functions::get_global_variable( 'view_type' );
			$prefix		 = PT_CV_PREFIX;
			$view_id	 = $prefix . 'view-' . $pt_cv_id;

			$css		 = !empty( $pt_cv_glb[ 'view_styles' ] ) ? $pt_cv_glb[ 'view_styles' ] : array();
			$font_links	 = array();

			// Generate CSS of margin, padding settings
			$use_margin = in_array( $view_type, array( 'collapsible', 'timeline' ) ) ? true : false;
			self::_style_margin( $view_id, $view_styles[ 'item-margin' ], $css, ".{$prefix}content-item", $use_margin ? 'margin' : 'padding'  );

			// Change left, right margin of View according to item padding
			if ( !$use_margin ) {
				$item_margin = array_intersect_key( $view_styles[ 'item-margin' ], array( 'left' => '', 'right' => '' ) );
				foreach ( $item_margin as $key => $value ) {
					if ( trim( $value ) !== '' ) {
						$value		 = intval( $value );
						$assign_val	 = ( $value > 0 ) ? 1 - $value : 1;

						if ( isset( $view_styles[ 'margin' ][ $key ] ) && trim( $view_styles[ 'margin' ][ $key ] ) !== '' ) {
							$cur_val = intval( $view_styles[ 'margin' ][ $key ] );
							if ( $cur_val < $assign_val ) {
								$view_styles[ 'margin' ][ $key ] = $assign_val;
							}
						} else {
							$view_styles[ 'margin' ][ $key ] = $assign_val;
						}
					}
				}
			}
			self::_style_margin( $view_id, $view_styles[ 'margin' ], $css );

			self::_style_margin( $view_id, $view_styles[ 'item-padding' ], $css, self::_get_fields_wrapper(), 'padding' );

			// Generate CSS of font settings
			$style_settings = apply_filters( PT_CV_PREFIX_ . 'style_settings_data', $view_styles[ 'font' ] );
			self::_style_font( $view_id, $style_settings, $css, $font_links );

			// Border radius
			if ( !empty( $view_styles[ 'border-radius' ] ) ) {
				$border_radius	 = $view_styles[ 'border-radius' ];
				$css[]			 = sprintf( '#%1$s .img-rounded, #%1$s .' . PT_CV_PREFIX . 'mask { -webkit-border-radius: %2$spx %3$s; -moz-border-radius: %2$spx %3$s; border-radius: %2$spx %3$s; }', $view_id, (int) $border_radius, '!important' );
			}

			// Soft resize
			if ( PT_CV_Functions::get_global_variable( 'soft_resize' ) ) {
				$dimensions	 = PT_CV_Functions::get_global_variable( 'image_sizes' );
				$selector	 = apply_filters( PT_CV_PREFIX_ . 'soft_resize_selector', "#$view_id .cvp-responsive-image" );
				$css[]		 = "$selector { width: {$dimensions[ 0 ]}px; height: {$dimensions[ 1 ]}px; }";

				$dimensions = PT_CV_Functions::get_global_variable( 'image_sizes_others' );
				if ( $dimensions ) {
					$selector	 = "#$view_id .{$prefix}ocol:nth-child(2n+2) .cvp-responsive-image";
					$css[]		 = "$selector { width: {$dimensions[ 0 ]}px; height: {$dimensions[ 1 ]}px; }";
				}
			}

			// Other styles
			if ( isset( $view_styles[ 'others' ] ) ) {
				$other_styles = $view_styles[ 'others' ];

				if ( !empty( $other_styles[ 'text-align' ] ) ) {
					$css[] = sprintf( '#%s { text-align: %s; }', $view_id, $other_styles[ 'text-align' ] );
				}
			}

			return array(
				'css'	 => implode( "\n", $css ),
				'links'	 => $font_links,
			);
		}

		/**
		 * Generate CSS of margin settings
		 *
		 * @param string $view_id The unique id of view
		 * @param array  $margin  The margin settings of this view
		 * @param type   $css     Store generated CSS
		 * @param type   $item_selector     No thing or each content item
		 * @param type   $css_property      Padding or Margin
		 */
		static function _style_margin( $view_id, $margin, &$css, $item_selector = '', $css_property = 'margin' ) {
			$options	 = array( 'top', 'left', 'bottom', 'right' );
			$margin_css	 = array();

			foreach ( $options as $option ) {
				if ( isset( $margin[ $option ] ) && trim( $margin[ $option ] ) !== '' ) {
					$value = intval( $margin[ $option ] );
					if ( $css_property === 'padding' && $value < 0 ) {
						$value = 0;
					}
					$margin_css[] = sprintf( '%s-%s: %spx !important;', $css_property, $option, $value );
				}
			}

			if ( $margin_css ) {
				$css[] = sprintf( '#%s %s { %s }', $view_id, $item_selector, implode( ' ', $margin_css ) );
			}
		}

		/**
		 * Generate CSS for font settings
		 *
		 * @param string $view_id    The unique id of view
		 * @param array  $fonts_data The font settings of this view
		 * @param type   $css        Store generated CSS
		 * @param type   $font_links Store generated font link to including
		 */
		static function _style_font( $view_id, $fonts_data, &$css, &$font_links ) {
			global $pt_cv_id;
			$properties = array( 'family', 'family-text', 'style', 'size', 'size-tablet', 'size-mobile', 'color', 'bgcolor', 'decoration', 'weight', 'transform', 'text-align', 'lineheight', 'letterspacing', 'border-width', 'border-style', 'border-color' );

			// CSS selector for each field
			$prefix					 = PT_CV_PREFIX;
			$view_related_selector	 = "#$view_id ";
			$pagination_wrapper		 = "$view_related_selector + .{$prefix}pagination-wrapper";
			$filter_bar_selector	 = "[id^='{$prefix}filter-bar-{$pt_cv_id}']";
			$fields_selectors		 = array(
				'content-item'			 => array( '_EMPTY_', "$view_related_selector " . self::_get_fields_wrapper( 'background-color' ) ),
				'item-border'			 => array( '_EMPTY_', "#{$view_id}.{$prefix}post-border .{$prefix}content-item" ),
				'pinmas'				 => '',
				/** use this after added class 'pt-cv-title' for 'panel-heading' of Collapsible
				  'title'				 => 'a',
				  'title-hover'		 => 'a:hover',
				 *
				 */
				'title'					 => "a, $view_related_selector .panel-title",
				'title-hover'			 => array( '_EMPTY_', "$view_related_selector .{$prefix}title a:hover, $view_related_selector .panel-title:hover" ),
				'content'				 => ", $view_related_selector .{$prefix}content *:not(.{$prefix}readmore):not(style):not(script)",
				'mask'					 => array( '_EMPTY_', "$view_related_selector .{$prefix}content-item:hover .{$prefix}hover-wrapper::before" ),
				'mask-text'				 => array( '_EMPTY_', trim( $view_related_selector ) . ":not(.{$prefix}nohover) .{$prefix}mask *" ),
				'carousel-caption'		 => '',
				'meta-fields'			 => '*:not(.glyphicon)',
				'meta-fields-wrapper'	 => '',
				'specialp'				 => '*',
				'pficon'				 => '',
				'custom-fields'			 => '*',
				'price'					 => array( '_EMPTY_', "$view_related_selector .add_to_cart_button, $view_related_selector .add_to_cart_button *" ),
				'woosale'				 => array( '_EMPTY_', "$view_related_selector .woocommerce-onsale" ),
				'readmore'				 => '',
				'readmore:hover'		 => '',
				'more-inactive'			 => array( ", $pagination_wrapper .pagination a", $pagination_wrapper ),
				'more'					 => array( ", $pagination_wrapper .pagination .active a", $pagination_wrapper ),
				'filter-bar'			 => array( '_EMPTY_', "$filter_bar_selector .{$prefix}filter-option, $filter_bar_selector .dropdown-menu" ),
				'filter-bar-active'		 => array( '_EMPTY_', "$filter_bar_selector .active.{$prefix}filter-option, $filter_bar_selector .active .{$prefix}filter-option, $filter_bar_selector .selected.{$prefix}filter-option, $filter_bar_selector .dropdown-toggle" ),
				'filter-bar-heading'	 => array( '_EMPTY_', "$filter_bar_selector .{$prefix}filter-title" ),
				'gls-header'			 => '',
				'tao'					 => '',
			);
			$fields					 = array_keys( $fields_selectors );

			// Unset keys if features are not enabled
			if ( !PT_CV_Functions::get_global_variable( 'enable_shuffle_filter' ) ) {
				unset( $fields[ array_search( 'filter-bar', $fields ) ] );
			}

			// Css properties of fields
			$fields_css		 = array();
			$font_css		 = array();
			$responsive_css	 = array();

			// Get properties of fields from settings array
			foreach ( $fields as $field ) {
				foreach ( $properties as $property ) {
					if ( !empty( $fonts_data[ "$property-$field" ] ) ) {
						$fields_css[ $field ][ $property ] = $fonts_data[ "$property-$field" ];
					}
				}
			}

			// Generate output font Css for fields
			foreach ( $fields as $field ) {
				$field_css = array();
				foreach ( $properties as $property ) {
					if ( !empty( $fields_css[ $field ][ $property ] ) ) {
						$property_val = $fields_css[ $field ][ $property ];

						switch ( $property ) {

							case 'family':
								if ( $property_val === 'custom-font' ) {
									if ( !empty( $fields_css[ $field ][ 'family-text' ] ) ) {
										$property_val = sanitize_text_field( $fields_css[ $field ][ 'family-text' ] );
									} else {
										$property_val = '';
									}
								}
								if ( !empty( $property_val ) ) {
									$field_css[] = sprintf( "font-family: '%s', Arial, serif", $property_val );
								}

								break;

							case 'style':
								$field_css[] = sprintf( 'font-style: %s', esc_attr( $property_val ) );

								break;

							case 'weight':
								/**
								 * keep it backward compatible with older versions
								 * @since 4.2.1
								 */
								$fweight = esc_attr( $property_val );
								if ( $fweight === 'bold' ) {
									$fweight = apply_filters( PT_CV_PREFIX_ . 'font_bold', '600' );
								}

								$field_css[] = sprintf( 'font-weight: %s', $fweight );

								break;

							case 'size':
								$font_size	 = (int) $property_val;
								$field_css[] = sprintf( 'font-size: %spx', $font_size );
								if ( empty( $fields_css[ $field ][ 'lineheight' ] ) ) {
									$field_css[] = sprintf( 'line-height: 1.3' );
								}

								break;

							case 'size-tablet':
							case 'size-mobile':
								$font_size								 = (int) $property_val;
								$responsive_css[ $field ][ $property ]	 = array( sprintf( 'font-size: %spx !important', $font_size ) );

								break;

							case 'border-width':
							case 'border-style':
							case 'border-color':
								$val = esc_attr( $property_val );
								if ( $property === 'border-width' ) {
									$val.='px';
								}

								$field_css[] = sprintf( '%s: %s', str_replace( 'border-', 'border-top-', $property ), $val );
								$field_css[] = sprintf( '%s: %s', str_replace( 'border-', 'border-left-', $property ), $val );

								break;

							case 'lineheight':
								$property_val	 = str_replace( '%', '%%', $property_val );
								$field_css[]	 = sprintf( 'line-height: %s', esc_attr( $property_val ) );

								break;

							case 'letterspacing':
								$field_css[] = sprintf( 'letter-spacing: %s', esc_attr( $property_val ) );

								break;

							case 'transform':
								$field_css[] = sprintf( 'text-transform: %s', esc_attr( $property_val ) );

								break;

							case 'text-align':
								$align	 = esc_attr( $property_val );
								$apply	 = true;

								$display = 'block'; // text-align only applies to block elements
								if ( $field === 'readmore' || $field === 'readmore:hover' ) {
									if ( $align === 'right' ) {
										$display = 'inline-block';
									} elseif ( $align === 'center' ) {
										$display	 = 'table';
										$field_css[] = 'margin-left: auto';
										$field_css[] = 'margin-right: auto';
										$field_css[] = 'float: none';
									} elseif ( $align === 'justify' ) {
										$align = 'center';
									}

									if ( in_array( $align, array( 'left', 'right' ) ) ) {
										$field_css[] = sprintf( 'float: %s', $align );
									}
								} elseif ( $field === 'custom-fields' ) {
									if ( $align === 'right' ) {
										$field_css[] = 'margin-left: auto';
									} elseif ( $align === 'center' ) {
										$field_css[] = 'margin-left: auto';
										$field_css[] = 'margin-right: auto';
									}
								} elseif ( $field === 'meta-fields' ) {
									$apply								 = false;
									$font_css[ 'meta-fields-wrapper' ]	 = ".{$prefix}meta-fields {text-align: $align; clear: both}";
								}

								if ( $apply ) {
									$field_css[] = sprintf( 'display: %s', $display );
									$field_css[] = sprintf( 'text-align: %s', $align );
									$field_css[] = 'clear: both';
								}

								break;

							case 'color':
								if ( $field === 'readmore' && PT_CV_Functions_Pro::check_dependences( 'text-link' ) && $property_val === '#ffffff' ) {
									break;
								}

								$field_css[] = sprintf( 'color: %s', $property_val );

								break;

							case 'bgcolor':
								if ( $field === 'readmore' && PT_CV_Functions_Pro::check_dependences( 'text-link' ) ) {
									break;
								}

								$field_css[] = sprintf( 'background-color: %s', $property_val );

								break;

							case 'decoration':
								$field_css[] = sprintf( 'text-decoration: %s', esc_attr( $property_val ) );

								break;
						}
					}
				}

				$suffix = ' !important;';
				if ( in_array( $field, array( 'mask-text', 'item-border' ) ) ) {
					$suffix = ';';
				}

				// Force important to preventing overwritten by other styles
				foreach ( $field_css as $idx => $value ) {
					$field_css[ $idx ] = $value . $suffix;
				}

				// Only include if CSS property is not null
				if ( $field_css ) {
					$font_css[ $field ] = self::_field_css( $field, $fields_selectors, $field_css );
				}
			}

			// Prepend view id to each css property
			foreach ( $font_css as $field => $value ) {
				$field_selector		 = (array) $fields_selectors[ $field ];
				$prepend_selector	 = isset( $field_selector[ 1 ] ) ? $field_selector[ 1 ] . ' ' : $view_related_selector;
				$css[]				 = $prepend_selector . $value;

				if ( isset( $responsive_css[ $field ] ) ) {
					foreach ( $responsive_css[ $field ] as $key => $rcss ) {
						$tcss	 = $prepend_selector . self::_field_css( $field, $fields_selectors, $rcss );
						$media	 = strpos( $key, 'tablet' ) ? '@media (min-width: 768px) and (max-width: 991px)' : '@media (max-width: 767px)';
						$css[]	 = "$media {" . $tcss . "}";
					}
				}
			}

			// Generate font links
			foreach ( $fields as $field ) {
				if ( !empty( $fields_css[ $field ][ 'family' ] ) ) {
					if ( $fields_css[ $field ][ 'family' ] !== 'custom-font' ) {
						$font_links[] = $fields_css[ $field ][ 'family' ];
					}
				}
			}
		}

		/**
		 * CSS output of a field
		 *
		 * @param string $field
		 * @param array $fields_selectors
		 * @param array $field_css
		 * @return string
		 */
		static function _field_css( $field, $fields_selectors, $field_css ) {
			$field_selector	 = (array) $fields_selectors[ $field ];
			$append_selector = !empty( $field_selector[ 'append_selector' ] ) ? $field_selector[ 'append_selector' ] : '';

			$p_selector	 = '.' . PT_CV_PREFIX . $field . $append_selector;
			$c_selector	 = $field_selector[ 0 ];
			if ( $field_selector[ 0 ] == '_EMPTY_' ) {
				$p_selector	 = $c_selector	 = '';
			}

			return sprintf( '%s %s { %s }', $p_selector, $c_selector, implode( ' ', $field_css ) );
		}

		/**
		 * Filter output: buttons group
		 *
		 * @param string $class The wrapper class of group
		 * @param array  $items The content of buttons
		 * @param string $id    The ID of filter group
		 *
		 * @return string
		 */
		static function filter_html_btn_group( $class, $items, $id = 'sample', $idx_tax = 0, $btn_style = 'btn-primary' ) {
			$items_html	 = array();
			$items		 = PT_CV_Html_Pro::shuffle_add_all( $items, $idx_tax );

			foreach ( $items as $key => $text ) {
				$item_class		 = implode( ' ', array( 'btn', $btn_style, PT_CV_PREFIX . 'filter-option', ( $key === 'all' ) ? 'active' : '' ) );
				$items_html[]	 = sprintf( '<button type="button" class="%s" data-value="%s" data-sftype="button">%s</button>', esc_attr( $item_class ), esc_attr( $key ), $text );
			}
			$output = sprintf( '<div class="btn-group %s" id="%s">%s</div>', esc_attr( $class ), esc_attr( $id ), implode( '', $items_html ) );

			return $output;
		}

		/**
		 * Generate HTML output for array of items
		 *
		 * @return array
		 */
		static function _filter_list( $type, $items, $idx_tax = 0 ) {
			$items_html	 = array();
			$items		 = PT_CV_Html_Pro::shuffle_add_all( $items, $idx_tax );

			foreach ( $items as $key => $text ) {
				$items_html[] = sprintf( '<li class="%s"><a href="#" class="%s" data-value="%s" data-sftype="%s">%s</a></li>', ( $key === 'all' ) ? 'active' : '', PT_CV_PREFIX . 'filter-option', esc_attr( $key ), esc_attr( $type ), $text );
			}

			return $items_html;
		}

		/**
		 * Filter output: Breadcrumb
		 *
		 * @param string $class The wrapper class of group
		 * @param array  $items The content of buttons
		 *
		 * @return string
		 */
		static function filter_html_breadcrumb( $class, $items, $id = 'sample', $idx_tax = 0 ) {
			$items_html	 = self::_filter_list( 'breadcrumb', $items, $idx_tax );
			$output		 = sprintf( '<ol class="breadcrumb %s" id="%s">%s</ol>', esc_attr( $class ), esc_attr( $id ), implode( '', $items_html ) );

			return $output;
		}

		/**
		 * Filter output: Vertical dropdown
		 *
		 * @param string $class The wrapper class of group
		 * @param array  $items The content of buttons
		 * @param type   $id    The ID of filter bar
		 *
		 * @return string
		 */
		static function filter_html_vertical_dropdown( $class, $items, $id = 'dropdownMenu1', $idx_tax = 0, $btn_style = 'btn-primary' ) {
			$all_text = PT_CV_Functions_Pro::shuffle_filter_group_setting( $idx_tax );

			$items_html	 = self::_filter_list( 'dropdown', $items, $idx_tax );
			$output		 = sprintf(
				'<div class="dropdown btn-group %s" id="%s">
				<button class="btn %s dropdown-toggle" type="button" data-toggle="dropdown">%s<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
				%s
				</ul>
			</div>', esc_attr( $class ), esc_attr( $id ), esc_attr( $btn_style ), $all_text, implode( '', $items_html )
			);

			return $output;
		}

		/**
		 * Display menu of Glossary list
		 *
		 * @param array $characters
		 */
		static function glossary_menu( $characters ) {
			$lis = array();

			// Sort A-Z by Heading
			asort( $characters );

			// Prepend "All"
			if ( $characters ) {
				array_unshift( $characters, __( 'All', 'content-views-pro' ) );
			}

			foreach ( $characters as $idx => $character ) {
				$href	 = PT_CV_PREFIX . 'gls-' . PT_CV_Html_Pro::sanitize_glossary_heading( $character );
				$class	 = $idx == 0 ? 'class="pt-active"' : '';
				$text	 = esc_html( $character );
				$lis[]	 = sprintf( '<li><a href="#%s" %s>%s</a></li>', $href, $class, $text );
			}

			return sprintf( '<ul class="%s">%s</ul>', PT_CV_PREFIX . 'gls-menu', implode( '', $lis ) );
		}

		static function custom_readmore( $href ) {
			$dargs	 = PT_CV_Functions::get_global_variable( 'dargs' );
			$fargs	 = isset( $dargs[ 'field-settings' ] ) ? $dargs[ 'field-settings' ] : array();

			$btn_class	 = PT_CV_PREFIX . 'readmore ' . apply_filters( PT_CV_PREFIX_ . 'field_content_readmore_class', 'btn btn-success', $fargs );
			$text		 = PT_CV_Html::get_readmore_text( $fargs[ 'content' ] );

			return sprintf(
				'<a href="%s" class="%s" target="%s" %s>%s</a>', esc_url( $href ), $btn_class, '_blank', null, $text
			);
		}

		static function date_convert( $str, $format = 'Y-m-d' ) {
			$result	 = $str;
			$valid	 = false;

			if ( (int) $str > strtotime( '1970-02-01' ) ) {
				$result	 = date( $format, (int) $str );
				$valid	 = true;
			} else if ( function_exists( 'date_parse' ) ) {
				$date_obj = (object) date_parse( str_replace( '/', '-', $str ) );
				if ( isset( $date_obj->error_count ) && $date_obj->error_count === 0 ) {
					$hour	 = ($date_obj->hour ? $date_obj->hour : '00') . ':';
					$minute	 = ($date_obj->minute ? $date_obj->minute : '00') . ':';
					$second	 = ($date_obj->second ? $date_obj->second : '00');
					$time	 = strtotime( "{$date_obj->year}-{$date_obj->month}-{$date_obj->day} {$hour}{$minute}{$second}" );
					$result	 = date( $format, $time );
					$valid	 = true;
				}
			}

			return array( $result, $valid );
		}

		static function sanitize_glossary_heading( $heading ) {
			return str_replace( '%', '1', urlencode( $heading ) );
		}

		static function image_output( $width, $height, $attr ) {
			$hwstring	 = image_hwstring( $width, $height );
			$attr		 = apply_filters( 'cvp_get_attachment_image_attributes', $attr, null, null );
			$attr		 = array_map( 'esc_attr', $attr );

			$found_image = rtrim( "<img $hwstring" );
			foreach ( $attr as $name => $value ) {
				$found_image .= " $name=" . '"' . $value . '"';
			}
			$found_image .= ' />';

			return $found_image;
		}

		/**
		 * Add the "All" option
		 * @since 4.2.1
		 *
		 * @param type $items
		 * @param type $idx_tax
		 * @return type
		 */
		static function shuffle_add_all( $items, $idx_tax ) {
			if ( PT_CV_Functions::get_global_variable( 'enable_shuffle_filter' ) ) {
				$matched_types	 = in_array( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'taxonomy-filter-type' ), array( 'btn-group', 'breadcrumb' ) );
				$hide_all		 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'taxonomy-hide-all' );
				if ( !$matched_types || ($matched_types && !$hide_all) ) {
					$all_text	 = PT_CV_Functions_Pro::shuffle_filter_group_setting( $idx_tax );
					$items		 = array( 'all' => $all_text ) + $items;
				}
			}

			return $items;
		}

		static function collapsible_parent_id( $view_id = null, $cur_page = null ) {
			if ( !$view_id ) {
				global $pt_cv_id;
				$view_id = $pt_cv_id;
			}
			if ( !$cur_page ) {
				$cur_page = apply_filters( PT_CV_PREFIX_ . 'wrap_in_page', true ) ? PT_CV_Functions::get_global_variable( 'current_page' ) : 1;
			}

			return $view_id . $cur_page;
		}

	}

}