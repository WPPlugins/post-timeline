<?php
/**
 */

$p_post = get_post();


$p_content = $p_post->post_content;
get_header(); ?>
    <div class="p-tl-cont" id="p-tl-cont">
        <div class="container-min single-post-timeline">
                <?php the_title( '<h1 class="p-timeline-title">', '</h1>' ); ?>
                <div class="p-timeline-img">
                <?php echo get_the_post_thumbnail(); ?>
                </div>
                <div class="p-timeline-content">
                    <?php echo $p_content; ?>
                </div><!-- .entry-content -->
                <?php

                    // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                ?>
        </div>
    </div>
    <!-- .content-area -->
<?php get_footer();
