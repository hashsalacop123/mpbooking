<?php 
//Template Name: Contact Us

get_header(); ?>
<div class = "contact-us-main-wrapper">
    <div class="banner-pages" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/img/contact-us-2.jpg' ); ?>'); background-position: center top;">
        <div class = "container">
            <div class = "row">
                <div class = "col-lg-6 col-md-6 col-sm-12">
                    <h1>Contact Us</h1>
                    <p>Have questions or want to get in touch?</p>
                    <p>We'd love to hear from you.</p>
                </div>
                <div class = "col-lg-6 col-md-6 col-sm-12">
                </div>

            </div>
        </div>
    </div>

</div>

<section class = "contact-from">
    <div class = "container">
        <div class = "row">
            <div class = "col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class = "form-container">
                        <h2>Get in Touch</h2>
                        <div class = "contact-form-wrapper">
                        <?php if (have_posts()) : ?>
                            
                            <?php while (have_posts()) : the_post(); ?>
                                
                                
                                <div class="post-content">
                                    <?php the_content(); ?>
                                </div>

                            <?php endwhile; ?>

                        <?php endif; ?>
                        </div>

                    </div>
            </div>
            <div class = "col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class = "form-image">
                        <img src = "<?php echo get_template_directory_uri().'/img/pexels-lebih-dari-ini-3915826-5908430.jpg' ?>" class = "img-fluid">

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
                <h3>Frequenty Ask Questions</h3>
     <div id="sampleAccordion" class="accordion-custom">

    <!-- Item 1 -->
    <div class="card">
        <div class="card-header" id="headingOne">
            <h5 class="mb-0">
                <button class="btn btn-link d-flex justify-content-between align-items-center w-100" 
                        data-toggle="collapse" 
                        data-target="#collapseOne" 
                        aria-expanded="true">
                    
                    What should i bring to my first lesson?
                    <i class="fa fa-minus toggle-icon"></i>
                </button>
            </h5>
        </div>

        <div id="collapseOne" class="collapse show" data-parent="#sampleAccordion">
            <div class="card-body">
                This is the content for the first accordion item.
            </div>
        </div>
    </div>

    <!-- Item 2 -->
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex justify-content-between align-items-center w-100" 
                        data-toggle="collapse" 
                        data-target="#collapseTwo">
                    
How do i book a court
                    <i class="fa fa-plus toggle-icon"></i>
                </button>
            </h5>
        </div>

        <div id="collapseTwo" class="collapse" data-parent="#sampleAccordion">
            <div class="card-body">
                This is the content for the second accordion item.
            </div>
        </div>
    </div>

    <!-- Item 3 -->
    <div class="card">
        <div class="card-header" id="headingThree">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex justify-content-between align-items-center w-100" 
                        data-toggle="collapse" 
                        data-target="#collapseThree">
                    
Are group lessons available?                    <i class="fa fa-plus toggle-icon"></i>
                </button>
            </h5>
        </div>

        <div id="collapseThree" class="collapse" data-parent="#sampleAccordion">
            <div class="card-body">
                This is the content for the third accordion item.
            </div>
        </div>
    </div>

</div>



            </div>
            <div class = "col-lg-4 col-xl-4 col-md-4 col-sm-12">
                <div class="banner-pages data-banner-member" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/img/pass.jpg' ); ?>'); background-position: center top;">
                    <h3>Join our tennis community today!</h3>
                    <a href = "/register/">Become a member</a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>