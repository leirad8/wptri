<?php /* Template Name: Members Template */ 
	get_header(); ?>
        <!-- members page -->
		<div id="page">
			<div class="section-container">
        		<div class="component--page--main-content">
        			<?php while ( have_posts() ) : the_post(); ?>
                    <h1 class="h1 text-align-center component--page--page-title"><?php the_title(); ?></h1>
                    <div class="component--page--main-content--body component--wysiwyg">
                        <?php the_content(); ?>
                    </div>  
                    <?php endwhile; ?>        
        		</div>
    		</div>
        </div>
        <!-- end members page -->
<?php get_footer(); ?>