<?php /* Template Name: News Template */

get_header(); ?>
<!-- news page -->
<div id="page">
 <div class="section-container">
  <div class="component--page--main-content">

	  <h1 class="h1 text-align-center component--page--page-title"><?php the_title(); ?></h1>
	  <ul class="news-feed">
	   <?php
		 $args = array(
			 'category_name' => 'news',
			 'posts_per_page' => 5,
			 'paged' => $paged
		 );
		 $wp_query = new WP_Query($args);
	   if ($wp_query->have_posts()) : while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

			<li id="post-<?php the_ID(); ?>" <?php // post_class(); ?>>
		 	 <h3 class="postListTitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		 	 <span class="date"> <?php echo get_the_date('m-d-Y', '', '', false); ?> </span>
		 	</li>

		 	<?php endwhile; ?>

			<?php// wp_reset_postdata(); ?>

			<?php else : ?>

		 	<li <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		 	 <h3>Not Found</h3>
		 	</li>

		 	<?php endif; ?>

		 	<div class="pagination">
		 	 <?php wp_pagination($wp_query->max_num_pages); ?>
		 	</div>

			<?php wp_reset_query(); ?>

		</ul>

  </div>
 </div>
</div>

<?php get_footer(); ?>
