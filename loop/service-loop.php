<?php 
               $query = new WP_Query($args);

                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post(); ?>

                   <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 court-item-wrapper remove-bootstrap">
                        <?php 
                            $image = get_field('featured_image');
                            $address =  get_field('address');
                            if ( $image && isset($image['url']) ) {
                                $image_url = $image['url'];
                            } else {
                                $image_url = get_stylesheet_directory_uri() . '/img/placeholder-400x400.jpg';
                            }

                        ?>

                        <a href = "<?php echo get_the_permalink(); ?>"><div class="item-wrapper-court" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                            <h3><i class="fa fa-trophy" aria-hidden="true"></i> <?php echo get_the_title(); ?></h3>
                           <div class = "wrapper-bot-comments">
                            <h5><i class="fa fa-location-arrow" aria-hidden="true"></i> <?php echo $address; ?></h5>
                                <div class = "service-review">
                                     <?php echo hash_display_rating_summary(); ?>
                                </div>
                            </div>
                        </div></a>
                    </div>
                    <?php endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>No posts found</p>';
                    endif;
?>