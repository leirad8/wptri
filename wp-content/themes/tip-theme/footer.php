		<!-- footer -->
		<footer class="footer">

<aside class="fatfooter" role="complementary">
    <div class="one-fifth widget-area first">
        <?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
    </div><!-- .first .widget-area -->
 
    <div class="one-fifth widget-area second">
        <?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
    </div><!-- .second .widget-area -->
 
    <div class="one-fifth widget-area third">
        <?php dynamic_sidebar( 'third-footer-widget-area' ); ?>
    </div><!-- .third .widget-area -->
 
    <div class="one-fifth widget-area fourth">
        <?php dynamic_sidebar( 'fourth-footer-widget-area' ); ?>
    </div><!-- .fourth .widget-area -->

    <div class="one-fifth widget-area fifth">
        <?php dynamic_sidebar( 'fifth-footer-widget-area' ); ?>
    </div><!-- .fifth .widget-area -->
</aside><!-- #fatfooter -->


            <div class="component--footer">

                <div class="component--footer--wrapper">
                    <div class="wrapper">
                        <span class="copyright">&copy; <?php echo date('Y'); ?> Telecom Infra Project. All rights reserved</span>

						<?php wp_nav_menu( array( 
		    				'theme_location' => 'footer',  
		    				'container_class' => 'menu-footer-menu-container',  
		    				'menu_id' => 'menu-footer-menu',
		        		) ); ?>


					</div>
                </div>
            </div>
        </footer>
        <!-- footer -->
		<div style="display:none">
		</div>
		<!-- wp_footer -->
		<?php wp_footer(); ?>
		<!-- end wp_footer -->
		<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/detectzoom.js'></script>
    </body>
</html>




