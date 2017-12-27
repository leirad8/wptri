<?php /* Template Name: OnePager Template */ 
	get_header(); ?>

        <div id="page">
			<div class="component--fullpage">
            	<div class="component--fullpage--gradient"></div>
				<div class="component--fullpage--hint">
                	<span class="component--fullpage--hint-button show"></span>
            	</div>
	            <div id="component--fullpage--slider">
					<?php
					    // SHOW THE PAGE CONTENTS
					    while ( have_posts() ) : the_post(); 
					   		 the_content();  
					    endwhile; 
					?>
	            </div>
	        </div>  
        </div>
<?php get_footer(); ?>
