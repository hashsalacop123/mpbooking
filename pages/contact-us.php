<?php 
//Template Name: Contact Us

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

<section class = "contact-from">
    <div class = "container">
        <div class = "row">
            <div class = "col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class = "form-container">
                         <div class = "contact-form-wrapper">
                        <?php
                            $contact_heading = get_field('contact_heading');
                            $paragraph_after_heading = get_field('paragraph_after_heading');
                            $image_left = get_field('image_left');
                           

                            if($contact_heading) :
                                echo '<h2>'.esc_attr($contact_heading).'</h2>';
                            endif;

                            $content = get_field('paragraph_after_heading');
                            
                            if ( $content ) {
                                echo do_shortcode( $content );
                            }
                          
                        ?>
                       
                      
                        </div>

                    </div>
            </div>
            <div class = "col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class = "form-image">
                        <?php 
                            if($image_left['url']) :
                            echo '<img src="' . esc_url($image_left['url']) . '" alt="' . esc_attr($image_left['alt']) . '" class="img-fluid">';    
                        endif;
                        ?>

                    </div>
            </div>
        </div>
    </div>

</section>

<section class = "icons-container">
    <div class = "container">
        <div class ="row">
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "data-contant">
                    <ul>
                        <li>
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            <h4>Call Us</h4>
                            <a href = "tel:+639215369904">+0921-536-9904</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            <h4>Email Us</h4>
                            <a href = "mailto:Matchpointtennis@outlook.ph
 ">Matchpointtennis@outlook.ph</a>

                        </li>

                    </ul>
                </div>
                
            </div>
             <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
              <div class = "data-contant">
                    <ul>
                        <li>
                            <i class="fa-solid fa-map-pin"></i>
                            <h4>Visit Us</h4>
                            IT Park, Lahug, Cebu City
                        </li>
                        <li>
                            <i class="fa-solid fa-house"></i>  
                                <h4>Follow Us</h4>
                            <ul class = "social-media contact-social-media">
                                <li><a href = "https://www.facebook.com/MatchpointTennisAcad/" target = "_blank"><i class="fa-brands fa-facebook"></i></a></li>
                                <li><a href = "https://www.youtube.com/@matchpointtenniscebu" target = "_blank"><i class="fa-brands fa-youtube"></i>
                                    </a></li>
                                <li><a href = "https://www.instagram.com/mptenniscebu" target = "_blank"><i class="fa-brands fa-square-instagram"></i>
                                    </a></li>
                                <li><a href = "hhttps://www.tiktok.com/@matchpointtenniscebu" target = "_blank"><i class="fa-brands fa-tiktok"></i>
                                    </a></li>
                            </ul>

                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<section class = "faq-wrapper">
    <div class = "container">
        <div class = "row align-items-stretch">
            <div class = "col-lg-8 col-xl-8 col-md-8 col-sm-12">
                     <?php 
                            $faq_heading = get_field('faq_heading');
                            $faq = get_field('faq');
                            $banner_heading = get_field('banner_heading');

                            if($faq_heading) :
                                echo '<h3>'.esc_attr($faq_heading).'</h3>';
                            endif;
                ?>
           
<?php if (have_rows('faq')) : ?>
    <div id="sampleAccordion" class="accordion-custom">

        <?php $i = 1; // counter for unique IDs ?>

        <?php while (have_rows('faq')) : the_row(); 
            $heading = get_sub_field('heading_title');
            $content = get_sub_field('faq_textarea');

            // Unique IDs
            $heading_id = 'heading' . $i;
            $collapse_id = 'collapse' . $i;

            // First item open by default
            $is_first = ($i === 1);
        ?>

            <div class="card">
                <div class="card-header" id="<?php echo $heading_id; ?>">
                    <h5 class="mb-0">
                        <button 
                            class="btn btn-link d-flex justify-content-between align-items-center w-100 <?php echo !$is_first ? 'collapsed' : ''; ?>" 
                            data-toggle="collapse" 
                            data-target="#<?php echo $collapse_id; ?>" 
                            aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>">
                            
                            <?php echo esc_html($heading);
                                $faqtextarea = get_sub_field('faqtextarea');
                            ?>

                            <i class="fa <?php echo $is_first ? 'fa-minus' : 'fa-plus'; ?> toggle-icon"></i>
                        </button>
                    </h5>
                </div>

                <div 
                    id="<?php echo $collapse_id; ?>" 
                    class="collapse <?php echo $is_first ? 'show' : ''; ?>" 
                    data-parent="#sampleAccordion">
                        
                    <div class="card-body">
                        <?php echo esc_attr($faqtextarea); ?>
                    </div>
                </div>
            </div>

        <?php $i++; endwhile; ?>

    </div>
<?php endif; ?>


            </div>
            <div class = "col-lg-4 col-xl-4 col-md-4 col-sm-12">
                <div class="banner-pages data-banner-member" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/img/pass.jpg' ); ?>'); background-position: center top;">
                    <?php 
                        $banner_heading = get_field('banner_heading');

                        if($banner_heading) :
                            echo '<h3>'.esc_attr($banner_heading).'</h3>';
                        endif;
                        
                       if ( is_user_logged_in() ) {
                            echo '<a href = "/dashboard/">Dashboard</a>';
                        } else {
                            echo '<a href = "/register/">Become a member</a>';
                        }
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>