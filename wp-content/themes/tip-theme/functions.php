<?php
/*
 * TIP functions.php
 */

 /*------------------------------------*\
     Google Analytics
 \*------------------------------------*/

function google_analytics() {
  ?>
  <!-- Global Site Tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?=GOOGLE_ANALYTICS_ID?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments)};
    gtag('js', new Date());
    gtag('config', '<?=GOOGLE_ANALYTICS_ID?>');
  </script>
  <?php
}
add_action( 'wp_head', 'google_analytics' );

function google_tracking() {
  ?>
    <!-- Google Tag Manager -->
    <script>
      (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?=GOOGLE_TRACKING_CONTAINER_ID?>');
    </script>
    <!-- End Google Tag Manager -->
  <?php
}
add_action( 'wp_head', 'google_tracking' );

function google_tracking_body() {
  ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?=GOOGLE_TRACKING_CONTAINER_ID?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  <?php
}
add_action( 'after_body_open_tag', 'google_tracking_body' );

/*function fb_share_body() {
  ?>
    <!-- Fb Share -->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <!-- End Fb Share -->
  <?php
}
add_action( 'after_body_open_tag', 'fb_share_body' );*/

// Load TIP scripts in header
function tip_header_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

      // disable jquery that comes with wp (conflicts with tip site) when not admin
      if( !is_admin()){
          wp_deregister_script('jquery');
          wp_register_script('jquery', get_template_directory_uri() . '/js/jquery.js', array(), '1.12.3');
          //wp_register_script('jquery', ("//ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"), false, '1.3.2');
          wp_enqueue_script('jquery');
      }

      //wp_register_script('detectzoom', get_template_directory_uri() .'/js/detectzoom.js', array(), '1.0.4'); //->MIN
      //wp_enqueue_script('detectzoom');
      //<script type='text/javascript' src='< ?php echo get_template_directory_uri(); ? >/js/detectzoom.js'></script>

      wp_register_script('isotope', '//unpkg.com/isotope-layout@3.0/dist/isotope.pkgd.min.js', array(), '3.0.1');
      wp_enqueue_script('isotope');

      wp_register_script('fullPage', '//cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.7.7/jquery.fullPage.min.js?ver=1', array(), '2.7.7');
      wp_enqueue_script('fullPage');

      wp_register_script('inputmask', '//cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.2.7/jquery.inputmask.bundle.min.js?ver=1', array(), '3.2.7');
      wp_enqueue_script('inputmask');

      wp_register_script('validate', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js?ver=1', array(), '1.14.0');
      wp_enqueue_script('validate');

      wp_register_script('autosize', '//cdnjs.cloudflare.com/ajax/libs/autosize.js/3.0.15/autosize.min.js?ver=1', array(), '3.0.15');
      wp_enqueue_script('autosize');

      wp_register_script('tip_js', get_template_directory_uri() . '/js/scripts.js', array(), '1.0');
      wp_enqueue_script('tip_js');
  }
}

// Load TIP styles
function tip_styles()
{

    wp_register_style('tip', get_template_directory_uri() . '/css/tip.css', array(), '1.0', 'all');
    wp_enqueue_style('tip'); // Enqueue it!
    wp_register_style('social', get_template_directory_uri() . '/css/social.css', array(), '1.0', 'all');
    wp_enqueue_style('social'); // Enqueue it!
    //<link rel='stylesheet' id='all-css-0' href="< ? php echo get_template_directory_uri(); ? >/css/tip.css" type='text/css' media='all'>

}

// add tags and categories to media
function tip_add_categories_to_attachments() {
    register_taxonomy_for_object_type( 'category', 'attachment' );
}

// apply tags to attachments
function tip_add_tags_to_attachments() {
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}

// Load Flexbox Grid styles
// http://flexboxgrid.com/
function flexbox_grid_styles()
{
    wp_register_style('flexbox-grid', get_template_directory_uri() . '/css/flexboxgrid.min.css', array(), '1.0', 'all');
    wp_enqueue_style('flexbox-grid'); // Enqueue it!
}

// Load registration Scripts
function tip_summit_registration_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
      wp_register_script('tip_summit_reg_js', get_template_directory_uri() . '/js/tip-summit-reg-js.js', array(), '1.0');
      wp_enqueue_script('tip_summit_reg_js');
  }
}
add_action('init', 'tip_summit_registration_scripts'); // Add Custom Scripts to wp_head

/*------------------------------------*\
    Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'tip_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_enqueue_scripts', 'tip_styles'); // Add Theme Stylesheet
add_action('init' , 'tip_add_categories_to_attachments'); // Add categories to media
add_action('init' , 'tip_add_tags_to_attachments'); // Add tags to media

// Load Tip Refresh Styles
function tip_refresh_styles()
{
    // load TIP refresh OLNLY and remove any other styles
    wp_register_style('tip_refresh', get_template_directory_uri() . '/css/tip-refresh/tip-refresh.css', array(), '1.0', 'all');
    wp_enqueue_style('tip_refresh');
    wp_dequeue_style('social');
    wp_dequeue_style('tip');
}

function tip_noninvasive_refresh_styles()
{
    // append TIP refresh to any other styles being loaded on page
    wp_register_style('tip_noninvasive_refresh', get_template_directory_uri() . '/css/tip-refresh/tip-noninvasive-refresh.css', array(), '1.0', 'all');
    wp_enqueue_style('tip_noninvasive_refresh');
}

function tip_refresh_vc_styles()
{
    // load TIP VC styles and remove any others
    wp_register_style('tip_refresh_vc', get_template_directory_uri() . '/css/tip-refresh/tip-refresh-vc.css', array(), '1.0', 'all');
    wp_enqueue_style('tip_refresh_vc');
    wp_dequeue_style('social');
    wp_dequeue_style('tip');
}

add_theme_support( 'title-tag' );

/*------------------------------------*\
    Remove various things ...
\*------------------------------------*/

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

function disable_wp_emojicons() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  //add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
add_action( 'init', 'disable_wp_emojicons' );


/*------------------------------------*\
    ShortCodes for onepager frontpage
\*------------------------------------*/


function shortcode_intro( $atts, $content = null ) {
    return '
    <div class="section transparent ">
                        <div class="circles-1"></div>
                        <div class="section-wrapper intro">' . $content . '</div>
                    </div>';
}
add_shortcode( 'intro_section', 'shortcode_intro' );


function shortcode_other_section( $atts, $content = null ) {
    return '
    <div class="section transparent ">
                        <div class="circles-1"></div>
                        <div class="section-wrapper ">' . $content . '</div>
                    </div>';
}
add_shortcode( 'section', 'shortcode_other_section' );


function shortcode_button_links( $atts, $content = null ) {
    return '                    <div class="section bg-extra-lt-grey solid wide">
                        <div class="circles-1"></div>
                        <div class="section-wrapper ">' . $content . '</div>
                    </div>';
}
add_shortcode( 'button_links', 'shortcode_button_links' );


/*------------------------------------*\
    Add some menus
\*------------------------------------*/

function register_my_menu() {
  register_nav_menu('main',__( 'Main Menu' ));
  register_nav_menu('footer',__( 'Footer Menu' ));
}
add_action( 'init', 'register_my_menu' );


/*------------------------------------*\
    Load other libraries
\*------------------------------------*/


// Register Custom Navigation Walker
require_once('wp_pagination.php');


/*------------------------------------*\
    ShortCodes for Groups pages
\*------------------------------------*/

function shortcode_projects_links_start( $atts, $content = null ) {
    return '                    </div><div class="component--panel-feed">
                        <ul class="component--panel-feed--list groups-list">';
}
add_shortcode( 'projects_links_start', 'shortcode_projects_links_start' );

function shortcode_projects_links_end( $atts, $content = null ) {
    return '                        </ul>
                      </div>';
}
add_shortcode( 'projects_links_end', 'shortcode_projects_links_end' );

function shortcode_project_link( $atts, $content = null ) {
    return '                    <li class="component--panel-feed--list-item">
                                  <a class="component--panel-feed--list-button" href="'.$atts{'link'}.'">
                                    <div class="component--panel-feed--list-button--wrapper">
                                      <h3>'.$atts{'title'}.'</h3>
                                    </div>
                                  </a>
                                </li>';
}
add_shortcode( 'project_link', 'shortcode_project_link' );

function shortcode_button_links_group( $atts, $content = null ) {
    return '                    <div class="section bg-extra-lt-grey solid wide">
                        <div class="section-wrapper ">' . $content . '</div>
                    </div>';
}
add_shortcode( 'button_links_group', 'shortcode_button_links_group' );

/*----------------------------------------------------------*\
                     Footer widget code
\*----------------------------------------------------------*/
function tutsplus_widgets_init() {

    // First footer widget area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'First Footer Widget Area', 'tutsplus' ),
        'id' => 'first-footer-widget-area',
        'description' => __( 'The first footer widget area', 'tutsplus' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    // Second Footer Widget Area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'Second Footer Widget Area', 'tutsplus' ),
        'id' => 'second-footer-widget-area',
        'description' => __( 'The second footer widget area', 'tutsplus' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    // Third Footer Widget Area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'Third Footer Widget Area', 'tutsplus' ),
        'id' => 'third-footer-widget-area',
        'description' => __( 'The third footer widget area', 'tutsplus' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    // Fourth Footer Widget Area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'Fourth Footer Widget Area', 'tutsplus' ),
        'id' => 'fourth-footer-widget-area',
        'description' => __( 'The fourth footer widget area', 'tutsplus' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

       // Fifth Footer Widget Area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'Fifth Footer Widget Area', 'tutsplus' ),
        'id' => 'fifth-footer-widget-area',
        'description' => __( 'The fifth footer widget area', 'tutsplus' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

}

// Register sidebars by running tutsplus_widgets_init() on the widgets_init hook.
add_action( 'widgets_init', 'tutsplus_widgets_init' );


// add tag support to pages
function tags_support_all() {
	register_taxonomy_for_object_type('post_tag', 'page');
}

// ensure all tags are included in queries
function tags_support_query($wp_query) {
	if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
}

// tag hooks
add_action('init', 'tags_support_all');
add_action('pre_get_posts', 'tags_support_query');
