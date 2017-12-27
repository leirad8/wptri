
<?php get_header(); ?>

<div id="page">
 <div class="section-container">
  <div class="component--page--main-content">
   <h1 class="h1 text-align-center component--page--page-title"><?php single_cat_title(); ?></h1>

		 	<ul class="news-feed">

				 <?php

          $args = array(
            'category_name' =>  'testing',
          //  'cat' => get_query_var('cat'),
            'posts_per_page' => 5,
            'paged' => $paged
          );
          $wp_query = new WP_Query($args);
					while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

					<li id="post-<?php the_ID(); ?>">
					 <h3 class="postListTitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					 <span class="date"> <?php echo get_the_date('m-d-Y', '', '', false); ?> </span>
					</li>

					<?php endwhile; ?>
          <?php // wp_reset_postdata(); ?>

				 <div class="pagination">
		     <?php wp_pagination($wp_query->max_num_pages); ?>
				 </div>

		 	</ul>

	 </div> <!-- /component-page-main-content -->
 </div> <!-- /section-container -->
</div> <!-- /page -->

<?php get_footer(); ?>
