<?php /* Template Name: Members Template Automated */ 

    // query all media
    $query_images_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => - 1,
        'order'          => 'ASC',
        'orderby'        => 'title', 
    );

    $query_images = new WP_Query( $query_images_args );

    $images = array();
    foreach ( $query_images->posts as $image ) {
        /*echo "<pre>";
        var_dump($image);
        echo "</pre>";*/

        if (in_category('logo', $image->ID)) {
                
                // get the slug of tags into a new array
                $tags = array();
                foreach (wp_get_post_tags($image->ID) as $tag) {
                    $tags[] = $tag->slug;
                }

                // add for first character class filter
                $tags[] = 'first_char_' . strtolower(substr(get_the_title($image->ID),0,1));
        
                $one_image = array(
                        'url' => wp_get_attachment_url($image->ID),
                        'tags' => $tags, // slugs from tags only
                    );
                $images[] = $one_image;
                //$images[] = wp_get_attachment_url( $image->ID );
        }
    }

	get_header(); ?>
        <!-- members page -->
		<div id="page">
			<div class="section-container">
        		<div class="component--page--main-content">
        			<?php while ( have_posts() ) : the_post(); ?>
                    <h1 class="h1 text-align-center component--page--page-title"><?php the_title(); ?></h1>
                    <div class="component--page--main-content--body component--wysiwyg">
                        <div class="component--member-grid">
                            <?php the_content(); ?>
                            
                            <div class="grid component--member-grid"> 
                            
                            <?php foreach ($images as $img) { // loop trough images ?>

                                
                                <div class="grid-item <?php echo implode(' ', $img['tags']) ?> ">
                                    <span class="grid-helper"><img src="<?php echo $img['url'] ?>" /></span>
                                </div>


                            <?php } // foreach $images ?>
                            </div>

                        </div>  
                    </div>
                    <?php endwhile; ?>        
        		</div>
    		</div>
        </div>
        <!-- end members page -->
<?php get_footer(); ?>