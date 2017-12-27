<?php /* Template Name: Login Members Template */ 
	get_header(); ?>
    <!-- members page -->
    <div id="page">
        <div class="section-container">
            <div class="component--page--main-content">
                <?php while ( have_posts() ) : the_post(); ?>
                    <h1 class="h1 text-align-center component--page--page-title"><?php the_title(); ?></h1>
                    <div class="component--page--main-content--body component--wysiwyg">
                     
                            <div class="row">
                                <div>Username:</div>
                                <div>
                                    <input id="user1" type="text" name="Username">
                                </div>
                            </div>
                            <div class="row">
                                <div>Password:</div>
                                <div>
                                    <input id="pw1" type="password" name="pw">
                                </div>
                            </div>
                            <div class="row">
                                <div></div>
                                <div>
                                    <input id="submit" type="submit" value="Submit">
                                </div>

                            </div>
                            <div class="row">
                                <div class="forgot-pw">
                                    <a href="#">Forgot password?</a>
                                </div>
                            </div>

                        <div class="row">
                            <p id="label1"></p>
                            <p id="reValue1"></p>

                            <p id="label2"></p>
                            <p id="reValue2"></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
            </div>
        </div>
    </div>
    <!-- end members page -->
    <?php get_footer(); ?>