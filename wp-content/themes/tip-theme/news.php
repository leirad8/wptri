<?php /* Template Name: News Template */ 
	get_header(); ?>
        <!-- news page -->
		<div id="page">
	        <div class="section-container">
                <div class="component--page--main-content">
                    <h1 class="h1 text-align-center component--page--page-title"><?php the_title(); ?></h1>
                    <ul class="news-feed">
                        <?php //the_content(); ?>
            			<?php 
                            while ( have_posts() ) : the_post(); 
                                $temp = $wp_query;
                                $wp_query= null;
                                $wp_query = new WP_Query();
                                $wp_query->query('category_name=news&posts_per_page=5'.'&paged='.$paged);
                                while ($wp_query->have_posts()) : $wp_query->the_post();
                        ?>
                        <!-- news item -->
                        <li id="post-<?php the_ID(); ?>">
                            <!-- img class="category-icon" src="<?php echo get_template_directory_uri(); ?>/img/icons/icon-category-news-black.png"-->
                            <h3 class="postListTitle"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <span class="date"> <?php echo get_the_date('m-d-Y', '', '', false); ?> </span>
                            <?php // the_excerpt(' '); ?> 
                        </li>
                        <!-- end news item -->
                        <?php endwhile; ?>
                        <!-- paginator -->
                        <div class="pagination">
                            <?php wp_pagination($wp_query->max_num_pages); ?>
                        </div>
                        <!-- end paginator -->
                        <?php $wp_query = null; $wp_query = $temp; ?>                    
                        <?php //$the_query = new WP_Query( $args ); ?>
                    </ul>   
                    <?php endwhile; ?>        
                    </div>
        		</div>
    		</div>
        </div>
        <!-- end news page -->
<?php get_footer(); ?>