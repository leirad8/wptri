<?php
	add_action('wp_enqueue_scripts', 'flexbox_grid_styles');
	add_action('wp_enqueue_scripts', 'tip_refresh_styles');
	get_header();

	// build Fb share link
	$uri = "http://telecominfraproject.com{$_SERVER['REQUEST_URI']}";
	$encodedUrl = urlencode(htmlspecialchars( $uri, ENT_QUOTES, 'UTF-8' ));
	$fbShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . $encodedUrl . htmlspecialchars('&amp;src=sdkpreparse');
?>

	<!-- Fb Share -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<!-- End Fb Share -->

	<!-- single -->
	<!-- <div class="header-spacer"></div> -->
	<div id="page" class="clearfix">
	    <!-- <aside class="component--article--breadcrumb mobile ">
	        <div class="wrapper">
	            <a href="/news/" class="component--article--breadcrumb-link">News</a>
	        </div>
	    </aside> -->
	    <div class="component--article--main-content component--wysiwyg">
	        <!-- <aside class="component--article--breadcrumb ">
	            <div class="wrapper">
	                <a href="/news/" class="component--article--breadcrumb-link">News</a>
	            </div>
	        </aside> -->
	        <?php while ( have_posts() ) : the_post(); ?>

						<?php
							// buid html for post metadata
							$articleDate = the_date('F j, Y','','',false);
							// build tags
							$articleTags = '';
							$posttags = get_the_tags();
							if ($posttags) {
							  foreach($posttags as $tag) {
									$articleTags .= '<a class="tag" href="/tag/'.$tag->slug.'">'.$tag->name.'</a>';
							  }
							}
							// build metadata html
							$htmlPostMetaData = <<<EOD
								<div class="article-meta-data-wrapper clearfix">
									<div class="lg-float-left">
										<!-- post date -->
										<div class="article-date">Posted on $articleDate</div>
										<!-- social media -->
										<div class="social-wrapper">
											<div class="fb">
												<!-- facebook -->
												<div class="fb-share-button"
												data-href="$uri"
												data-layout="button_count"
												data-size="small"
												data-mobile-iframe="true">
													<a class="fb-xfbml-parse-ignore" target="_blank" href="$fbShareUrl">Share</a>
												</div>
											</div>
											<div class="linkedin">
												<!-- linked in -->
												<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
												<script type="IN/Share"></script>
												<!-- <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
												<script type="IN/Share" data-url="$url"></script> -->
											</div>
										</div>
									</div>
									<div class="lg-float-right">
										<!-- tags -->
										<!-- commented out for now till search results page created $articleTags -->
									</div>
								</div>
EOD;
// EOD; closing tag must not be indented
						?>

						<div class="row-tip">
							<div class="box">
								<div class="header-wrapper clearfix">
									<? // CoChairs ?>
									<? /*<div class="co-chairs-wrapper clearfix">
										<?php
											$postMetaData = get_post_meta(get_the_ID(), 'co_chair', false);
											foreach ($postMetaData as $key => $value) {
												$coChair = explode(";", $value);?>
												<div class="co-chair">
													<strong>Co-chaired by <?=$coChair[0]?></strong>
													<?=$coChair[1]?> <?=$coChair[2]?>
												</div>
												<?
											}
										?>
									</div>*/ ?>

									<? // Authors ?>
									<div class="component--article--author-info">
											<span>by <?php echo get_the_author(); ?></span>
									</div>


									<? // page title ?>
									<h2 class="component--page--page-title"><?php the_title(); ?></h2>

									<? // Article Meta Data ?>
									<?=$htmlPostMetaData?>

								</div>
							</div>
						</div>

			        <? /*<div class="component--article--author-info">
			            <span>by <?php echo get_the_author(); ?>.</span>
			        </div> */ ?>

							<? // WYSIWYG content ?>
							<div class="row-tip">
								<div class="box">
			        		<?php the_content(); ?>
								</div>
							</div>

							<? // Article Meta Data ?>
							<div class="row-tip">
								<div class="box">
									<?=$htmlPostMetaData?>
								</div>
							</div>

	    		<?php endwhile; ?>

					<? // Previous and Next links ?>
					<div class="row-tip">
						<div class="box">
							<div class="clearfix md-margin-top-lg article-nav">
								<div class="float-left"><?php previous_post_link(''.'%link','<div class="icon icon-arrow-left"></div>Previous Article'); ?></div>
								<div class="float-right"><?php next_post_link('%link'.'','Next Article<div class="icon icon-arrow-right"></div>'); ?></div>
							</div>
						</div>
					</div>

	    </div>

			<?php /*include_once( 'widgets//mailchimp-signup-form/index.php' );*/ ?>

			<? // Membership Benifits ?>
			<article class="banner">
				<div class="row">
					<div class="image col-xs-12 col-md-6">
						<div class="box">
							<img src="/wp-content/uploads/become-member.jpg" />
						</div>
					</div>
					<div class="text col-xs-12 col-md-6">
						<div class="box verticall-align-items">
							<h2>Membership Benefits</h2>
							<p>Join one of our Project Groups to collaborate with Operators, Infrastructure Providers, and Integrators in conceiving new and innovative ways of building and deploying telecom network infrastructure.</p>
							<div class="button-wrapper small-white-border">
								<a href="/tip-summit-2017-registration/" class="button">Become a Member</a>
							</div>
						</div>
					</div>
				</div>
			</article>

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
