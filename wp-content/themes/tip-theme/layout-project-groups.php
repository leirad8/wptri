<?php /* Template Name: Project Groups */
	add_action('wp_enqueue_scripts', 'flexbox_grid_styles');
	add_action('wp_enqueue_scripts', 'tip_refresh_styles');
	get_header();

	// build Fb share link
	//$url =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
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

    <!-- Project Group page -->
		<div class="header-spacer"></div>
		<div id="page">
			<div class="section-container">
        		<div class="component--page--main-content">
	        			<?php while ( have_posts() ) : the_post(); ?>

									<div class="row-tip">
										<div class="box">
											<div class="header-wrapper clearfix">
												<? // CoChairs ?>
												<div class="co-chairs-wrapper">
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
												</div>

												<div class="social-wrapper">
													<div class="fb">
														<? // Fb Share link ?>
														<div class="fb-share-button"
														data-href="<?=$uri?>"
														data-layout="button_count"
														data-size="small"
														data-mobile-iframe="true">
															<a class="fb-xfbml-parse-ignore" target="_blank" href="<?=$fbShareUrl?>">Share</a>
														</div>
													</div>

													<div class="linkedin">
														<? // LinkedIn Share link ?>
														<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
														<script type="IN/Share"></script>
														<!-- <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
														<script type="IN/Share" data-url="<?=$url?>"></script> -->
													</div>
												</div>
											</div>

											<? // page title ?>
											<h2 class="component--page--page-title"><?php the_title(); ?></h2>

										</div>
									</div>

									<? // WYSIWYG content ?>
	                <div class="component--page--main-content--body component--wysiwyg">
	                    <?php the_content(); ?>
	                </div>

									<div class="row-tip">
										<div class="box">

											<? // Related blog posts ?>
											<?php /*
											<h2>Blog Posts</h2>
											<?php the_tags( 'Tagged with: ', ' - ', '<br />' ); ?>
											<?php
												// list post titles related to first tag on current post
												$tags = wp_get_post_tags($post->ID);
												if ($tags) {
													$first_tag = $tags[0]->slug;
													$args = array(
														'posts_per_page' => 6,
														'tag' => $first_tag,
														'orderby' => 'date',
														'order' => 'ASC',
													);

													$my_query = new WP_Query($args);

													if( $my_query->have_posts() ) {
														while ($my_query->have_posts()) : $my_query->the_post(); ?>
															<a href="<?php the_permalink() ?>"
																rel="bookmark"
																title="Permanent Link to <?php the_title_attribute(); ?>"
																style="border: solid 1px #000; padding: 10px; margin: 10px 20px 10px 0; display: inline-block;"><?php the_title(); ?></a>
														<?php
														endwhile;
													}
												wp_reset_query();
												}
											?>
											*/?>

											<? // Additional Info ?>
											<?php
												$postMetaData = get_post_meta(get_the_ID(), 'attachment_pdf_url', false);
												if(sizeof($postMetaData) >= 1) {
													echo('<h2>Additional Info</h2>');
												}
												foreach ($postMetaData as $key => $value) {
													$addlInfoFile = explode(";", $value); ?>
												  <a href="<?=$addlInfoFile[1]?>" target="_blank" class="addl-info-link"><?=$addlInfoFile[0]?></a>
													<?
												}
											?>

											<div class="addl-info-links-wrapper">
												<?php $hideJoinProject = get_post_meta(get_the_ID(), 'hide_join_project', true);
												if($hideJoinProject != 'true') { ?>
													<div class="button-wrapper small-green">
												    <a href="/member-login/" class="button">Join Project</a>
												  </div>
												<? } ?>

												<?php $projectCharterURL = get_post_meta(get_the_ID(), 'project_charter_url', true);
												if($projectCharterURL != '') { ?>
													<a href="<?=$projectCharterURL?>" target="_blank">View Project Charter</a>
												<? } ?>

												<!--<a href="#">Workplace Group (TODO: add later)</a><br/>-->
											</div>

										</div>
									</div>

	              <?php endwhile; ?>

								<? // Membership Benifits ?>
        		</div>
    		</div>
        </div>
        <!-- end Project Group page -->
<?php get_footer(); ?>
