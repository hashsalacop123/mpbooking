<?php get_header(); ?>
<div class = "service-wrapper">
    <div class = "container">
        <div class = "row">
        <div class = "col-xl-9 col-lg-9 col-md-8 col-sm-12">
            <div class = "wrapper-data">

                <?php 
                $court_name = get_field('court_name_gym');
                    if($court_name ) {
                        echo '<h1>'.$court_name.'</h1>';
                    }else {
                        echo '<h1>'.get_the_title().'</h1>';
                    }
                
                ?>
                <div class="service-meta">
    <!-- Author -->
   

    <!-- Date -->
    <span class="service-date">
      Posted  on <?php echo get_the_date(); ?>
    </span>

    <!-- Genre (custom taxonomy) -->
    <?php 
    $terms = get_the_terms( get_the_ID(), 'genre' );
    if ( $terms && ! is_wp_error( $terms ) ) : 
        $genre_names = wp_list_pluck( $terms, 'name' ); // get names only
    ?>
        <span class="service-genre">
            | Genre: <?php echo esc_html( implode( ', ', $genre_names ) ); ?>
        </span>
    <?php endif; ?>
</div>
                <div class = "gallery-image">
                   <?php
$images = get_field('images');

if ( $images ) : ?>
    
    <!-- Main Slider -->
    <div class="service-gallery-main">
        <?php foreach ( $images as $image ) : ?>
            <div class="slide">
                <img 
                    src="<?php echo esc_url( $image['sizes']['large'] ); ?>" 
                    alt="<?php echo esc_attr( $image['alt'] ); ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Thumbnail Navigation -->
    <div class="service-gallery-thumbs">
        <?php foreach ( $images as $image ) : ?>
            <div class="thumb">
                <img 
                                src="<?php echo esc_url( $image['sizes']['thumbnail'] ); ?>" 
                                alt="<?php echo esc_attr( $image['alt'] ); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php endif; ?>
                </div>
                <!-- address data -->
                <div class = "divider-sp">

                    <hr>
                </div>
                <div class = "address-data">
                    <div class = "address-data-information">
                        <?php $address = get_field('address');?>
                        <h3><i class="fa fa-map-pin" aria-hidden="true"></i> <?php echo $address; ?></h3>

                    </div>
                    <div class = "share-and-heart">
                        
                    </div>
                </div>
                <div class = "service-maps">
                    <?php
                        $lat = get_field('address_lat');
                        $lng = get_field('address_lang');

                        if ( $lat && $lng ) : ?>
                            <div 
                                id="service-map"
                                data-lat="<?php echo esc_attr($lat); ?>"
                                data-lng="<?php echo esc_attr($lng); ?>"
                                style="width:100%; height:200px;">
                            </div>
                        <?php endif; ?>
                </div>
                    
                 <div class = "divider-sp">
                    <hr>
                </div>  
                <div class = "about-us-service">
                    <?php $about = get_field('about_me'); 
                        echo $about;
                    ?>

                </div>



            </div>
        </div>
                <!-- SIDEBAR START HERE -->
        <div class = "col-xl-3 col-lg-3 col-md-4 col-sm-12">
            <?php get_template_part( 'single-service/sidebar' ); ?>      
        </div>
          
        <!-- SIDEBAR CLOSED HERE -->
        </div>
    </div>
</div>
<div class = "single-booking-services">
        <div class = "container">
             <?php 

require_once get_template_directory() . '/inc/service-booking-calendar.php';

?>   <?php 
   echo '<div class="review-list">';
echo hash_display_rating_summary();
comments_template();
echo '</div>';
   ?>
                    </div>
                    
</div>
<!-- ShareThis BEGINS -->
<div class="sharethis-share-buttons" data-type="sticky-share-buttons">
    <span data-network="facebook"></span>
    <span data-network="twitter"></span>
    <span data-network="linkedin"></span>
    <span data-network="email"></span>
    <span data-network="sharethis"></span>
</div>
<!-- ShareThis ENDS -->
<?php get_footer(); ?>