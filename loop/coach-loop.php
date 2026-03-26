<?php
                    $query = new WP_Query($args);

                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post();

                        $nickname = get_field('nick_name');
                        $featured_image = get_field('featured_image');
                        $hourly = get_field('hourly_rate');
                        $city = get_field('city');

                    ?>
                    <div class = "col-xl-6 col-lg-6 col-md-6 col-sm-12 remove-bootstrap">
                      <a href = "<?php echo get_the_permalink(); ?>">
                        <div class = "coach-card coach-ratings-review">
                            <div class = "image-wrapper">
                                <?php 
                                     $default = get_stylesheet_directory_uri().'/img/placeholder-400x400.jpg';
                        
                                if($featured_image) {
                                    echo '<img src = "'.$featured_image['sizes']['large'].'" class = "img-fluid" alt = "'.$featured_image['alt'].'">';
                                }else {
                                        echo '<img src = "'.$default.'" class = "img-fluid" alt = "default">';
                                }
                                ?>
                            </div>
                            <div class = "coach-information">
                                <div class = "name-wrapper">
                                    <h4><?php echo $nickname; ?></h4>
                                    <h4>&#8369; <?php echo $hourly; ?>/hour</h4>
                                </div>
                                <div class = "about-wrapper">
                                     <i class="fa fa-quote-left" aria-hidden="true"></i>
                                 <?php
$about_me = get_field('about_me');

if ($about_me) {

    // Limit to 400 characters
    $short_text = mb_substr(strip_tags($about_me), 0, 80);

     echo esc_html($short_text) . '...';
}
?><i class="fa fa-quote-right" aria-hidden="true"></i>

                                </div>
                                <div class = "review">
                                    <div class = "count">
                                        <?php echo hash_display_rating_summary(); ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div> <!--card closed -->
                        </a>
                    </div>       
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>No posts found</p>';
                    endif;
                    ?>