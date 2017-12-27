<?php /* Template Name: Project Template */ 
	get_header(); ?>
        <!-- project -->
		<div id="page">
            <aside class="component--article--breadcrumb mobile">
                <div class="wrapper">
                    <a href="/projects/" class="component--article--breadcrumb-link">Projects</a>
                </div>
            </aside>
            <div class="component--article--main-content component--wysiwyg project">
                <aside class="component--article--breadcrumb">
                    <div class="wrapper">
                        <a href="/projects/" class="component--article--breadcrumb-link" >Projects</a>
                    </div>
                </aside>
                <aside class="component--article--info"></aside>

                <?php while ( have_posts() ) : the_post(); ?>
                <h1 class="h1 project"><?php the_title(); ?></h1>
                <?php the_content(); ?>
                <?php endwhile; ?>

            </div>
        </div>
        <!-- end project -->
<?php get_footer(); ?>
