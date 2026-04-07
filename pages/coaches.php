<?php 
// Template Name: Coaches 

get_header(); 
    
    // TOP HEADER OF SOME PAGES
    include get_template_directory() . '/inc/top-header.php'; 

?>

        <h1><?php echo get_the_title();?></h1>

           <?php
                if ( have_posts() ) :
                    while ( have_posts() ) :
                        the_post(); ?>
                                <?php the_content(); ?>

                        <?php
                    endwhile;
                endif;
                ?>
    </div> 
    <!-- this is the close div of the banner -->
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
