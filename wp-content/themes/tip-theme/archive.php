
<?php get_header(); ?>

<!-- archive page -->
<div id="page">
 <div class="section-container">
  <div class="component--page--main-content">

	  <h1 class="h1 text-align-center component--page--page-title"><?php the_archive_title(); ?></h1>

    <div class="vc_row wpb_row vc_row-fluid"><div class="wpb_column vc_column_container vc_col-sm-12">
     <div class="vc_column-inner ">
      <div class="wpb_wrapper">
       <div class="vc_row wpb_row vc_inner vc_row-fluid">
        <div class="wpb_column vc_column_container vc_col-sm-12">
         <div class="vc_column-inner ">
          <div class="wpb_wrapper">
    	     <div class="wpb_text_column wpb_content_element ">
    		    <div class="wpb_wrapper">
    			   <div class="pt-cv-wrapper">

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

              </div>
             </div>
            </div>
           </div>
          </div>
         </div>
        </div>
       </div>
      </div>
     </div>
  

  </div>
 </div>
</div>

<?php get_footer(); ?>
