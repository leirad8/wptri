
<?php get_header(); ?>

<!-- news page -->
<div id="page">
 <div class="section-container">
  <div class="component--page--main-content">

	  <h1 class="h1 text-align-center component--page--page-title"><?php echo get_the_title(143); // News page id ?></h1>
	  <ul class="news-feed">

		 <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<li id="post-<?php the_ID(); ?>" <?php // post_class(); ?>>
		 	 <h3 class="postListTitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		 	 <span class="date"> <?php echo get_the_date('m-d-Y', '', '', false); ?> </span>
		 	</li>

			<?php endwhile; ?>

		 <?php else : ?>

			<li <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			 <h3>Not Found</h3>
			</li>

		 <?php endif; ?>

		<div class="pagination">
		 <?php wp_pagination(); ?>
		</div>

	  </ul>

  </div>
 </div>
</div>

<?php get_footer(); ?>
