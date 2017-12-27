<?php 
	get_header(); ?>
        <!-- single -->
		<div id="page" class="clearfix">
            <aside class="component--article--breadcrumb mobile ">
                <div class="wrapper">
                    <a href="/news/" class="component--article--breadcrumb-link">News</a>
                </div>
            </aside>
            <div class="component--article--main-content component--wysiwyg">
                <aside class="component--article--breadcrumb ">
                    <div class="wrapper">
                        <a href="/news/" class="component--article--breadcrumb-link">News</a>
                    </div>
                </aside>
                <?php while ( have_posts() ) : the_post(); ?>                
                <aside class="component--article--info">
                    <span class="component--article--postdate">Posted <span class="toggle-visibility">on</span> <?php the_date(); ?></span>
                </aside>

                <h1 class="h1"><?php the_title(); ?></h1>
                
                <div class="component--article--author-info">
                    <span>by <?php echo get_the_author(); ?>.</span>
                </div>
                
                <?php the_content(); ?>

                <div id="jp-post-flair" class="sharedaddy sd-like-enabled sd-sharing-enabled"><div class="sharedaddy sd-sharing-enabled"><div class="robots-nocontent sd-block sd-social sd-social-icon sd-sharing"><h3 class="sd-title">Share:</h3><div class="sd-content"><ul><li class="share-facebook"><a rel="nofollow" data-shared="sharing-facebook-701" class="popup share-facebook sd-button share-icon no-text" href="http://www.facebook.com/sharer.php?u=<?php echo esc_url( get_permalink() ); ?>&t=<?php the_title(); ?>" target="_blank" title="Share on Facebook"><span></span><span class="sharing-screen-reader-text">Share on Facebook (Opens in new window)</span></a></li><li class="share-twitter"><a rel="nofollow" data-shared="sharing-twitter-701" class="popup share-twitter sd-button share-icon no-text" href="http://twitter.com/share?text=<?php echo urlencode(the_title()); ?>&url=<?php echo esc_url( get_permalink() ); ?>" target="_blank" title="Click to share on Twitter"><span></span><span class="sharing-screen-reader-text">Click to share on Twitter (Opens in new window)</span></a></li><li class="share-end"></li></ul></div></div></div></div>

                
            <?php endwhile; ?>
                
            <div class="post-content"></div>

            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
               $('.popup').click(function() {
                 var NWin = window.open($(this).prop('href'), '', 'scrollbars=1,height=420,width=500');
                 if (window.focus)
                 {
                   NWin.focus();
                 }
                 return false;
                });
            });
        </script>
        <!-- end single -->
<?php get_footer(); ?>