<?php 
// Template Name: Landing Page

get_header(); ?>


<div class="landing-page-wrapper home-landing-page" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/img/banner-home.jpg' ); ?>');">  <!-- Video Background -->
    <!-- Fallback image if video can't play -->
 
<?php 
 $heading_text = get_field('heading_text');
 $subtext = get_field('subtext');
 $court_heading = get_field('court_heading');
 $court_subtext = get_field('court_subtext');
 $number_court_display = get_field('number_court_display');
 $coach_heading = get_field('coach_heading');
 $coach_subtext = get_field('coach_subtext');
 $number_of_coach = get_field('number_of_coach');


?>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="content-landing-page">
          <?php 
              if($heading_text) : 
                echo '<h1>'.esc_attr($heading_text).'<h1>';
              endif; 

              if($subtext) : 
                echo '<h2>'.esc_attr($subtext).'<h2>';
              endif; 
          ?>
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

              <?php 
                    if($court_heading) : 
                      echo '<h3 class = "h3-class">'.esc_attr($court_heading).'</h3>';
                    endif; 

                    if($court_subtext) : 
                      echo '<p>'.esc_attr($court_subtext).'</p>';
                    endif; 

                    // $number_court_display = '0';

                ?>
              </div>
            <?php 
                        $args = array(
                            'post_type'      => 'service',
                            'posts_per_page' => $number_court_display,
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
               <?php 
                    if($coach_heading) : 
                      echo '<h3 class = "h3-class">'.esc_attr($coach_heading).'</h3>';
                    endif; 

                    if($coach_subtext) : 
                      echo '<p>'.esc_attr($coach_subtext).'</p>';
                    endif; 

                ?>
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