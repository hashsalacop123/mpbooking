<?php 
//Template Name: About Us

get_header(); 

$heading_title = get_field('heading_title');
$subheading = get_field('subheading');

// get featured image URL
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');

// fallback image
$default_image = get_stylesheet_directory_uri() . '/img/banner-image.jpg';

// use featured if exists, otherwise fallback
$bg_image = $featured_image ? $featured_image : $default_image;
?>
<div class="banner-pages" style="background-image: url('<?php echo esc_url($bg_image); ?>');">


    <div class = "container">
        <div class = "row">
            <div class = "col-lg-6 col-md-6 col-sm-12">
                    <?php 
                         if($heading_title) : 
                            echo '<h1>'.esc_attr($heading_title).'</h1>';
                        endif; 

                        if($subheading) : 
                            echo wp_kses_post($subheading);
                        endif; 
                    ?>

                </div>
             <div class = "col-lg-6 col-md-6 col-sm-12">
            </div>

        </div>
    </div>

</div>
<section class = "who-we-are-wrapper">
    <div class = "container">
        <div class = "row">
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "about-first-col">
<?php 
    $image_right = get_field('image_right');
    $image_subheading = get_field('image_subheading');
    $image_text = get_field('image_text');

    if($image_right['url']) :
        echo '<img src = "'.$image_right['url'].'" class = "img-fluid">';
    endif;
?>

                </div>
            </div>
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "about-second-col">
                    <?php 
                         if($image_subheading) : 
                            echo '<h2>'.esc_attr($image_subheading).'</h2>';
                        endif; 

                         if($image_text) : 
                            echo wp_kses_post($image_text);
                        endif; 
                    ?>
            </div>
        </div>
    </div>
</section>

<section class = "our-mission">
    <div class = "container">
        <div class = "row">

<?php
    $our_mission_main_heading = get_field('our_mission_main_heading');
    $our_mission_heading = get_field('our_mission_heading');
    $text_our_mission = get_field('text_our_mission');
    $our_vision_heading = get_field('our_vision_heading');
    $text_our_vision = get_field('text_our_vision');
    $our_mission_image = get_field('our_mission_image');
?>
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "our-mission-first-col">
                    <?php 
                           if($our_mission_main_heading) : 
                            echo '<h2>'.esc_attr($our_mission_main_heading).'</h2>';
                        endif;
                    ?>
                    <ul class = "our-mission-list">
                        <li> <img src = "<?php echo get_template_directory_uri().'/img/tennis-player.png' ?>" class = "img-fluid">

                            <div class = "colab">
                                  <?php 
                                    if($our_mission_heading) : 
                                        echo '<h4>'.esc_attr($our_mission_heading).'</h4>';
                                    endif;

                                    if($text_our_mission) : 
                                        echo wp_kses_post($text_our_mission);
                                    endif; 
                                ?>
                            </div>
                        </li>
                          <li>  
                   
                          <img src = "<?php echo get_template_directory_uri().'/img/community.png' ?>" class = "img-fluid">

                            <div class = "colab">
                        <?php 
                                    if($our_vision_heading) : 
                                        echo '<h4>'.esc_attr($our_vision_heading).'</h4>';
                                    endif;

                                    if($text_our_vision) : 
                                        echo wp_kses_post($text_our_vision);
                                    endif; 
                                ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "our-mission-second-col">
                         <?php 
                            if($our_mission_image) :
                                echo '<img src = "'.$our_mission_image['url'].'" class = "img-fluid">';
                            endif;
                        ?>

                </div>
            </div>
        </div>
    </div>
</section>
<section class = "our-programs">
    <div class = "container">
    <?php 
        $heading_program = get_field('heading_program');
        $programs = get_field('programs');

          if($heading_program) : 
            echo '<h4>'.esc_attr($heading_program).'</h4>';
        endif;
    ?>
    <?php if (have_rows('programs')) : ?>
    <ul class="programs-ul">

        <?php while (have_rows('programs')) : the_row(); ?>

            <?php
            // Get sub fields
            $image = get_sub_field('image_program');
            $title = get_sub_field('title');
            $desc  = get_sub_field('short_description');

            // fallback image (optional)
            $default_img = get_template_directory_uri() . '/img/promotion.png';

            // get image URL safely
            $image_url = $image ? $image['url'] : $default_img;
            ?>

            <li class="progrmas-li">

                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">

                <div class="programs-list">
                    <h5><?php echo esc_html($title); ?></h5>
                    <p><?php echo esc_html($desc); ?></p>
                </div>

            </li>

        <?php endwhile; ?>

    </ul>
<?php endif; ?>


    </div>
</section>


<?php get_footer(); ?>