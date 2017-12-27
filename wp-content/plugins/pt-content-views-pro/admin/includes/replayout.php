
<?php

class CVP_Replace_Setting {

	private static $instance;
	protected $archives_list;
	protected $views_list;
	protected $field_archive;
	protected $saved_data;

	public static function get_instance() {
		if ( !CVP_Replace_Setting::$instance ) {
			CVP_Replace_Setting::$instance = new CVP_Replace_Setting();
		}

		return CVP_Replace_Setting::$instance;
	}

	public function __construct() {
		$this->field_archive = CVP_REPLAYOUT;

		$this->get_archives();
		$this->get_views_list();
		$this->show_form();
	}

	function get_archives() {
		$taxes	 = array();
		$arr	 = get_taxonomies( array( 'public' => true ), 'objects' );
		foreach ( $arr as $taxonomy ) {
			if ( $taxonomy->name === 'post_format' ) {
				continue;
			}

			$taxes[ $taxonomy->name ] = $taxonomy->label;
		}

		$post_types	 = array();
		$arr		 = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );
		foreach ( $arr as $post_type ) {
			$post_types[ $post_type->name ] = $post_type->labels->singular_name;
		}

		// Ignore attachment for now
		$post_types_builtin = array(
			'post'	 => __( 'Post' ),
			'page'	 => __( 'Page' ),
		);

		$this->archives_list = array(
			-1				 => array( __( 'Common Issues', 'content-views-pro' ), array(
					'show-heading'	 => __( 'Fix the missing page title (title of archives pages disappear when replacing layout)', 'content-views-pro' ),
					'full-width'	 => __( 'Fix the cramped widths of layout', 'content-views-pro' ),
				) ),
			0				 => array( __( 'Standard Archives', 'content-views-pro' ), array(
					'home'	 => __( 'Blog' ),
					'search' => __( 'Search results', 'content-views-pro' ),
					'author' => __( 'Author' ),
					'time'	 => __( 'Date, Month, Year', 'content-views-pro' ),
				) ),
			'tax'			 => array( __( 'Taxonomy Archives', 'content-views-pro' ), $taxes ),
			'post_type'		 => array( __( 'Post Type Archives', 'content-views-pro' ), $post_types ),
			'is_singular'	 => array( __( 'Post Type Single', 'content-views-pro' ), array_merge( $post_types_builtin, $post_types ) ),
		);
	}

	function get_views_list() {
		$this->views_list = cvp_get_view_list();
	}

	function show_form() {
		$this->save_form();
		?>
		<style>
			.wrap h4{margin-top:0;margin-bottom:5px}
			input[type=checkbox]{opacity:.5}
			input[type=checkbox]:checked{opacity:1}
			.cvp-notice, .cvp-notice *{font-size:16px}
		</style>
		<script>
			( function ( $ ) {
				$( document ).ready( function () {
					$( 'select', '.cvp-admin' ).select2();
				} );
			} )( jQuery );
		</script>
		<div class="wrap">
			<h2><?php _e( 'Replace Theme Layout with Content Views Pro', 'content-views-pro' ) ?></h2>
			<br>
			<?php $this->show_notice(); ?>
			<br>
			<div class="pt-wrap cvp-admin">
				<form action="" method="POST">
					<input type="submit" class="btn btn-primary pull-right" value="<?php _e( 'Save' ); ?>" style="margin-top: -40px;">
					<div class="clearfix"></div>
					<?php
					wp_nonce_field( PT_CV_PREFIX_ . 'view_submit', PT_CV_PREFIX_ . 'form_nonce' );

					$sort_options	 = array( '' => __( '(Default sort order)', 'content-views-pro' ), 'use_view_order' => __( 'Use "Sort by" setting of View', 'content-views-pro' ) );
					$comment_options = array( '' => __( 'Hide comments', 'content-views-pro' ), 'show_comment' => __( 'Show comments', 'content-views-pro' ) );

					foreach ( $this->archives_list as $idx => $archive_type ) {
						$heading = $archive_type[ 0 ];
						$pages	 = $archive_type[ 1 ];

						if ( !$pages ) {
							continue;
						}

						printf( '%s<h4># %s</h4>', $idx === -1 ? '' : '<br><hr class="clear">', $heading );
						foreach ( $pages as $page => $title ) {
							$name		 = ( $idx ? $idx . '-' : '') . $page;
							$field_name	 = esc_attr( $this->field_archive . "[$name]" );
							$page_data	 = !empty( $this->saved_data[ $name ] ) ? $this->saved_data[ $name ] : null;

							echo '<div class="clear">';


							$show_all_columns	 = $idx >= 0;
							$first_col_width	 = $show_all_columns ? 4 : 12;
							# Page name
							printf( '<div class="col-md-' . $first_col_width . '">
									<div class="checkbox">
										<label for="%1$s">
											<input type="checkbox" id="%1$s" name="%1$s" value="%2$s" %3$s>%4$s
										</label>
									</div>
								</div>', $field_name . '[rep_status]', 'enable', !empty( $page_data[ 'rep_status' ] ) ? 'checked' : '', $title );

							if ( $show_all_columns ) {
								# View
								$options		 = array();
								$selected_view	 = !empty( $page_data[ 'selected_view' ] ) ? $page_data[ 'selected_view' ] : '';
								foreach ( $this->views_list as $view_id => $title ) {
									$options[] = sprintf( '<option value="%s" %s>%s</option>', esc_attr( $view_id ), selected( $selected_view, $view_id, false ), esc_html( $title ) );
								}
								printf( '<div class="col-md-5">
									<select name="%s" class="form-control">%s</select>
								</div>', $field_name . '[selected_view]', implode( '', $options ) );

								# Sort by/Comments
								$attribute	 = ($idx !== 'is_singular') ? 'sort_by' : 'show_comment';
								$array		 = ($idx !== 'is_singular') ? $sort_options : $comment_options;

								$options	 = array();
								$selected	 = !empty( $page_data[ $attribute ] ) ? $page_data[ $attribute ] : '';
								foreach ( $array as $val => $_title ) {
									$options[] = sprintf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( $selected, $val, false ), esc_html( $_title ) );
								}
								printf( '<div class="col-md-3">
									<select name="%s" class="form-control">%s</select>
								</div>', $field_name . "[$attribute]", implode( '', $options ) );
							}

							echo '</div>';
						}
					}
					?>

					<div class="clearfix"></div>
					<hr>
					<input type="submit" class="btn btn-primary pull-right" value="<?php _e( 'Save' ); ?>">
				</form>
			</div>
		</div>
		<?php
	}

	function show_notice() {
		$msg	 = $more	 = array();
		$msg[]	 = __( 'When you select each checkbox below, Content Views Pro will replace posts layout of selected page by layout of selected View.', 'content-views-pro' );
		$more[]	 = sprintf( __( '- You should %s create new View %s for replacing purpose', 'content-views-pro' ), '<strong>', '</strong>' );
		$more[]	 = sprintf( __( '- Most settings on "%s" tab of selected View %s will not be applied %s', 'content-views-pro' ), __( 'Filter Settings', 'content-views-query-and-display-post-page' ), '<strong>', '</strong>' );
		$msg[]	 = sprintf( '<p>%s</p>', implode( '<br>', $more ) );

		printf( '<div class="cvp-notice">%s</div>', implode( ' ', $msg ) );
	}

	function save_form() {
		if ( !empty( $_POST[ $this->field_archive ] ) ) {
			$this->saved_data = $_POST[ $this->field_archive ];

			update_option( $this->field_archive, $this->saved_data, false );
		} else {
			$this->saved_data = get_option( $this->field_archive );
		}
	}

}

CVP_Replace_Setting::get_instance();
