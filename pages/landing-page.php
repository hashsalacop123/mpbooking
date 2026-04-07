<?php 
// Template Name: Landing Page

get_header(); ?>


<div class="landing-page-wrapper home-landing-page" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/img/banner-home.jpg' ); ?>');">  <!-- Video Background -->
    <!-- Fallback image if video can't play -->
 

  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="content-landing-page">
          <h1>Book Your Court or Coach Instantly</h1>
          <h2>Search courts, coaches, and locations near you in seconds.</h2>
       <div class="search-wrapper">
    <select id="coach-search" style="width:100%"></select>
    <button id="search-btn" type="button">Search</button>
</div>

</select>
        </div>
      </div>
    </div>
  </div>
</div>
  <div class = "court-section court-inner-wrapper">
      <div class = "container service-loop-landing">
          <div class = "row">
              <div class = "col-md-12">
                  <hr>
                <h3 class = "h3-class" >Find your court</h3>
                <p>Discover top courts in your area and reserve your slot with ease.</p>
              </div>
            <?php 
                        $args = array(
                            'post_type'      => 'service',
                            'posts_per_page' => 6,
                            'post_status'    => 'publish',
                            'orderby' => 'rand',
                        );

                        include get_template_directory() . '/loop/service-loop.php'; 

                      ?>
            </div>
        </div>
    </div>
<div class = "coach-section coach-inner-wrapper">
  <div class = "container">
      <div class = "row">
          <div class = "col-md-12">
            <hr>
            <h3 class = "h3-class">Find your coach</h3>
            <p>Connect with experienced coaches and improve your game.</p>
          </div>
         <?php 
                    $args = array(
                        'post_type'      => 'coach',
                        'posts_per_page' => 6,
                        'post_status'    => 'publish',
                        'orderby' => 'rand',
                    );

                     include get_template_directory() . '/loop/coach-loop.php'; 

                   ?>
        </div>
    </div>
  </div>


<?php get_footer(); ?>