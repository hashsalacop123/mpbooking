<?php 
// Template Name: Coaches 

get_header(); ?>
<div class = "coaches-wrapper">
    <div class="top-header-page" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/img/top-heading.jpg');">
        <h1><?php echo get_the_title();?></h1>
    </div>
    <div class = "coach-inner-wrapper">
        <div class = "container">
        <div class = "row">
            <?php 
                    $args = array(
                        'post_type'      => 'coach',
                        'posts_per_page' => -1,
                        'post_status'    => 'publish',
                    );

                     include get_template_directory() . '/loop/coach-loop.php'; 

                   ?>

            
        
        </div>
    </div>
    </div>
</div>

<?php get_footer(); ?>
