<?php /* Template Name: TIP Refresh VC Page */
	//add_action('wp_enqueue_scripts', 'flexbox_grid_styles');
	add_action('wp_enqueue_scripts', 'tip_refresh_vc_styles');
	get_header();
?>

		<main role="main" id="main-tip-refresh">
		<!-- section -->
		<section>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>
			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="header-spacer"></div>
				<?php the_content(); ?>
				<br class="clear">
			</article>
			<!-- /article -->
		<?php endwhile; ?>

		<?php else: ?>
			<!-- article -->
			<article>
				<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
			</article>
			<!-- /article -->

		<?php endif; ?>

		</section>
		<!-- /section -->
	</main>

<?php get_footer(); ?>
<?php include_once( 'widgets/popup-alert/index.php' ); ?>

<script type="text/javascript">
// var popups = document.getElementsByClassName('newsletter-popup')
//
// function showNewsletterPopup(event) {
// 	console.log('this',event.dataset.href);
// 	//window.location = event.dataset.href;
// }
//
// Object.keys(popups).forEach(function(popup, key){
// 	//console.log('popup:', popups[popup].dataset.href);
// 	popups[popup].addEventListener('click', function(){showNewsletterPopup(this)}, false);
// });


</script>
