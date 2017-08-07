<?php

/**
 *
 * This file is used to markup the public-facing pattern template.
 *
 * @since      0.0.1
 *
 * @package    Post_TIMELINE
 * @subpackage Post_TIMELINE/public/partials
 */

$colors = array("first","second","third","forth","fifth","sixth","seventh");



$main_image = wp_get_attachment_image_src($parent_post->custom['_thumbnail_id'][0] , 'large' );
$main_image = isset( $main_image['0'] ) ? $main_image['0'] : null;

$asl_site_url = site_url()."/post-timeline/";

// strip tags to avoid breaking any html
?>

<!-- Start of timeline -->
<div class="p-tl-cont">      
    <!-- head -->
    <div class="container timeline_details">
        <?php if($main_image): ?>
        <div class="col-md-6">
            <?php if(isset($parent_post->custom['_thumbnail_id'][0])): ?> 
            <div class="hexagon" style="background-image: url(<?php echo $main_image; ?>);">
              <div class="hexTop"></div>
              <div class="hexBottom"></div>
            </div>
            <?php endif; ?> 
        </div>
        <?php endif; ?>
        <div class="col-md-6 details-sec">
            <h1><?php echo $parent_post->post_title ?></h1>
            <h3><?php echo $parent_post->custom['post-timeline-image-overlay'][0] ?></h3>
            <p><?php echo $parent_post->post_content; ?></p>
            <p class="time-p"><span><?php echo $parent_post->time_range ?></span></p>
        </div>
    </div>
    <!-- head End -->
    
    <div class="container main-timeline post-timeline">
        
        <span class="line"></span> 
        <?php
        $year        = null;
        $year_change = false;
        $is_start    = null;
        $color       = 0;
        $row_index   = 0;


        foreach($child_posts as $c_post):


            $c_image = wp_get_attachment_image_src($c_post->custom['_thumbnail_id'][0] , 'large' );
            $c_image = isset( $c_image['0'] ) ? $c_image['0'] : null;

            $color_index = 0;

            $p_content = $c_post->post_content;

            if (strlen($p_content) > 350) {

                // truncate string
                $stringCut = substr($p_content, 0, 350);

                // make sure it ends in a word so assassinate doesn't become ass...
                $p_content = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
            }


            if($c_post->custom['post-timeline-post-color'][0])
                $color_index = $c_post->custom['post-timeline-post-color'][0];

            if($c_post->date_comp[0] != $year) {

                $is_start = ($year == null)?true:false;

                $year = $c_post->date_comp[0];
                $year_change = true;
            }
            else
                $year_change = false;
            


            if($year_change):

                //Put the previous year ending  div
                if(!$is_start) {
                    echo '</div>';
                }

            if($row_index == 1)    
                echo '</div>';
            
            $row_index = 0;

            ?>

            <!-- Year  -->
            <div class="row">
                <div class="col-md-5"></div>
                <div class="col-md-2">
                    <div class="row">
                        <div class="year">
                            <span><?php echo $year; ?></span>
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                 viewBox="0 0 441.3 512" style="enable-background:new 0 0 441.3 512;" xml:space="preserve">
                            <style type="text/css">
                                .st0{fill:#000;stroke:#000000;stroke-miterlimit:10;}
                            </style>
                            <polygon id="XMLID_1_" class="st0" points="1,129.2 220.7,2.3 440.4,129.2 440.4,382.8 220.7,509.7 1,382.8 "/>
                            </svg>

                        </div>
                    </div>
                </div>
                <div class="col-md-5"></div>
            </div>

            <div class="blank-space-90"></div>
            <!-- Year  End -->
        
            <div class="row timeline-section">        

            <?php endif; //year change ?> 
                
                <?php if($row_index % 2 == 0): ?>   
                <div class="row"> 
                <?php endif ?>
                    
                    <!-- post html -->
                    <div class="timeline-box <?php echo $colors[$color_index] ?>">
                        <h1><span><?php echo $c_post->post_title; ?></span> <div class="month-box"><?php echo $c_post->date_str ?></div></h1>

                        <?php if($c_post->custom['post-timeline-img-txt-pos'][0] == 1): ?> 
                        <p><?php echo $c_post->post_content;  ?></p>
                        <?php endif; ?> 
                        <?php if($c_post->custom['_thumbnail_id'][0]): ?> 
                        <?php if($c_image): ?>
                        <img alt="image" src="<?php echo $c_image; ?>" />
                        <?php endif; ?>
                        <?php if($c_post->custom['post-timeline-image-overlay'][0]): ?>  
                        <p class="post-caption"><?php echo $c_post->custom['post-timeline-image-overlay'][0] ?></p>
                        <?php endif ?>
                        <?php endif; ?>
                        <?php if($c_post->custom['post-timeline-img-txt-pos'][0] != 1): ?>  
                        <p class="ptl-desc"><?php echo $p_content;  ?></p>
                        <?php endif; ?>
                        <a class="at-read-more" href="<?php echo $asl_site_url.$c_post->post_name ?>">Read more</a>
                    </div>
                    <!-- post html end -->

                <?php if($row_index % 2 != 0): $row_index = 0; ?> 
                </div>
                <?php else:
                    $row_index++;
                    endif;
                 ?> 
        <?php
            $color++;
        endforeach;
        ?>

        <!-- the end of timeline div and the Ending Year -->
        <?php if(count($child_posts) > 0):

            if($row_index == 1)
                echo '</div>';
        ?>
        </div>

        <div class="row">
            <div class="col-md-5"></div>
            <div class="col-md-2" >
                <div class="row">
                    <div class="year"><span><?php echo $year; ?></span></div>
                </div>
            </div>
            <div class="col-md-5"></div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- END of timeline -->

<script type="text/javascript">

jQuery(function(){

    var apply_width = function() {
        
        //detect container width
        var p_width = jQuery('.p-tl-cont').width();

        if(p_width <= 769) {

                jQuery('.p-tl-cont').removeClass('col-md-12 col-lg-12').addClass('col-sm-12');
        }
        else if(p_width <= 992) {

                jQuery('.p-tl-cont').removeClass('col-sm-12 col-lg-12').addClass('col-md-12');
        }
        else if(p_width <= 1200) {

                jQuery('.p-tl-cont').removeClass('col-sm-12 col-md-12').addClass('col-lg-12');
        }
        else
            jQuery('.p-tl-cont').removeClass('col-sm-12 col-md-12 col-lg-12');//.addClass('p-tl-cont');
    };
    apply_width();

    jQuery(window).resize(function(e){
        apply_width();
    });


});
</script>