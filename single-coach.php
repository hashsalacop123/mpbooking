<?php get_header(); ?>
<div class="coach-wrapper " data-coach="<?php echo get_the_author_meta('ID'); ?>">
        <div class = "container">
            <div class = "row">

            <div class = "col-xl-5 col-lg-5 col-md-5 col-sm-12">
                <div class = "profile-pic">

                <?php 

$image_size = 'large';
$fallback_image = get_template_directory_uri() . '/img/placeholder-400x400.jpg';
$featured_image = get_field('featured_image');

if ( ! empty( $featured_image ) ) {
    $image_url = $featured_image['sizes'][ $image_size ] ?? $featured_image['url'];
    $image_alt = esc_attr( $featured_image['alt'] );
} else {
    $image_url = $fallback_image;
    $image_alt = 'Default image';
}
?>

<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo $image_alt; ?>">

                </div>
            </div>

            <div class = "col-xl-7 col-lg-7 col-md-7 col-sm-12">
                <div class = "wrapper-coach-info">
                    <?php $nickname = get_field('nick_name'); 
                          $sports = get_field('sports');

                          echo '<h1>'.$nickname.'</h1>';
                         if (!empty($sports) && is_array($sports)) {
                            echo '<ul>';

                            foreach ($sports as $sport) {
                                echo '<li>' . esc_html($sport->name) . '</li>';
                            }

                            echo '</ul>';
}
                     
                        echo '<div class = "additional-information-coach">';
                         echo '<div>';     
                               if ( $address = get_field('address') ) {
                                     echo '<b><i class="fa fa-home" aria-hidden="true"></i></b> ' . $address;
                                  }
                                echo '</div>';
                                 echo '<div>';     
                               if ( $phone = get_field('phone') ) {
                                     echo '<b><i class="fa fa-phone" aria-hidden="true"></i></b> ' . $phone;
                                  }
                                echo '</div>';
                               echo '<div>';     
                               if ( $gender = get_field('gender') ) {
                                     echo '<b><i class="fa fa-id-card" aria-hidden="true"></i></b> ' . $gender;
                                  }
                                echo '</div>';
                                echo '<div>';
                                if ( $hourly_rate = get_field('hourly_rate') ) {
                                     echo '<b><i class="fa fa-hourglass-start" aria-hidden="true"></i></b> ' .  $hourly_rate.' / hr';
                                  }
                                  echo '</div>';
                                  echo '<div class = "single-coach-btn">';
                                    echo '<a href = "#booknow"><i class="fa fa-bookmark" aria-hidden="true"></i> Book Now</a>';
                                    echo '<a href = "#review"><i class="fa fa-book" aria-hidden="true"></i>
  Review</a>';
                                  echo '</div>';
                        echo '</div>';
                    ?>
                    
                </div>
            </div>

            <div class = "col-lg-9 col-xl-9 col-md-12 col-xs-12">
                <div class = "wrappper-main-formation">
                    <h3>About Me</h3>
                    <?php $about = get_field('about_me'); ?>
                        <?php if($about) :
                            echo $about;
                        endif;  ?>
                    <div class = "gallery-wrapper">
                        <h3>Beyond the court</h3>
                        <?php
                        $gallery = get_field('mygallery');

                        if ( $gallery ) : ?>
                            <div class="coach-gallery">
                                <?php foreach ( $gallery as $image ) : ?>
                        <a 
                                        href="<?php echo esc_url( $image['url'] ); ?>"
                                        data-fancybox="coach-gallery"
                                        data-caption="<?php echo esc_attr( $image['caption'] ); ?>"
                                        class="coach-gallery-item"
                                    >
                                        <img 
                                            src="<?php echo esc_url( $image['sizes']['medium'] ); ?>" 
                                            alt="<?php echo esc_attr( $image['alt'] ); ?>"
                                        >
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                       
<?php 

require_once get_template_directory() . '/inc/coach-booking-calendar.php';

echo '<div class="review-list">';
echo hash_display_rating_summary();
comments_template();
echo '</div>';
?>


    </div>

</div>

            <div class = "col-lg-3 col-xl-3 col-md-12 col-xs-12">
                <div class = "coach-sidebar">
                    <div class = "related-coach">
                        <h4>Related Coach</h4>
                          
                                            <?php 
                    $args = array(
                        'post_type'      => 'coach',
                        'posts_per_page' => 4,
                        'post_status'    => 'publish',
                        'orderby' => 'rand',
                    );

                     include get_template_directory() . '/loop/coach-loop.php'; 

                   ?>
                                       
                    </div>
                
                <div class = "coach-review-wrapper">
                                
                    </div>
                </div>
                    
            </div>

            </div> <!-- row -->
        </div> <!-- container -->
</div> <!-- coach-wrapper -->

<?php get_footer(); ?>