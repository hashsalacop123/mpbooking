<?php 
//Template Name: Blog

get_header(); 

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
<div class = "container news-loop">
        <div class = "row">
                <?php
                    // Create a custom query
                    $args = array(
                        'post_type'      => 'post', // change this to your CPT slug
                        'posts_per_page' => 10, // number of posts
                    );

                // Run the query
                $query = new WP_Query($args);

                // Check if posts exist
                if ($query->have_posts()) :

                    // Start loop
                    while ($query->have_posts()) : $query->the_post();
                        ?>

                        <div class="post-item col-xl-4 col-lg-4 col-md-6 col-xs-12">
                            <!-- Post Title -->

                            <!-- Featured Image -->
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Post Content -->
                            <div class="post-content">
                                <h2><?php the_title(); ?></h2>
          <div class="post-meta d-flex flex-wrap gap-3 mb-4 text-muted small">

                    <!-- Category -->
                 

                    <!-- Date -->
                    <span>
                        <i class="fa fa-calendar me-1"></i>
                        <?php echo get_the_date(); ?>
                    </span>

                    <!-- Author -->
                    <span>
                        <i class="fa fa-user me-1"></i>
                        <?php the_author(); ?>
                    </span>

                </div>
<?php
echo wp_trim_words(get_the_excerpt(), 20, '...'); // limit to 20 words
?>                            </div>

                            <!-- Read More -->
                            <a href="<?php the_permalink(); ?>">Read More</a>
                        </div>

                        <?php
                    endwhile;

                    // Reset post data
                    wp_reset_postdata();

                else :
                    echo '<p>No posts found.</p>';
                endif;
                ?>
        </div>    
</div>
<?php get_footer(); ?>