<?php /* Template Name: TIP Summit Registration */
	add_action('wp_enqueue_scripts', 'flexbox_grid_styles');
	add_action('wp_enqueue_scripts', 'tip_refresh_styles');
	get_header(); ?>

		<main role="main">
		<!-- section -->
		<section>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>
			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php the_content(); ?>
				<?php include_once( 'pages/tip-summit-2017-registration.php' ); ?>
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
