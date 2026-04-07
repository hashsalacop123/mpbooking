<?php
/**
 * Template Name: single post page
 */

get_header();
?>
<div class="container my-5 single-blog">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <?php while (have_posts()) : the_post(); ?>

                <!-- Title -->
                <h1 class="mb-3"><?php the_title(); ?></h1>

                <!-- Meta -->
                <div class="post-meta d-flex flex-wrap gap-3 mb-4 text-muted small">

                    <!-- Category -->
                    <span>
                        <i class="fa fa-folder-open me-1"></i>
                        <?php the_category(', '); ?>
                    </span>

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
<?php if (has_post_thumbnail()) : ?>
                    <div class="mb-4">
                        <?php the_post_thumbnail('large', ['class' => 'img-fluid rounded']); ?>
                    </div>
                <?php endif; ?>
                <!-- Content -->
                <div class="post-content">
                    <?php the_content(); ?>
                </div>

            <?php endwhile; ?>
<?php 
   echo '<div class="review-list">';
echo hash_display_rating_summary();
comments_template();
echo '</div>';
   ?>
        </div>
    </div>
</div>
<div class="sharethis-share-buttons" data-type="sticky-share-buttons">
    <span data-network="facebook"></span>
    <span data-network="twitter"></span>
    <span data-network="linkedin"></span>
    <span data-network="email"></span>
    <span data-network="sharethis"></span>
</div>

<?php
get_footer();