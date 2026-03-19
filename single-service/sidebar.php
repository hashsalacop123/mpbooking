<div class = "sidebar-wrapper-single-service">
   
    <?php 
        $email = get_field('email');
        $website = get_field('website');
        $phone = get_field('phone');
        $socialmedia = get_field('socialmedia');
        
    ?>
    <div class = "contact-us-first-wrapper">
        <button class = "booknowdata"><i class="fa-regular fa-calendar-check"></i>
        <a href = "#booknow" class = "general-btn">Book Now</a></button>
        <h4>Hourly Rate</h4>

         <div class = "rates">
        <?php
         $hourly_rate = get_field('hourly_rate'); 
            
        ?>
        <i class="fas fa-hourglass-start"></i> <?php echo $hourly_rate.' PHP'; ?>
    </div>
          <h4>Contact Us Directly</h4>
            <ul>
                <?php 
                    if($email) {
                        echo '<li><a href = "mailto:'.$email.'"><i class="fa fa-envelope" aria-hidden="true"></i> Email</a></li>';
                    }
                ?>
                <?php 
                    if($website) {
                        echo '<li><a href = "'.$website.'" target = "_blank"><i class="fa fa-globe" aria-hidden="true"></i> Website</a></li>';
                    }
                ?>
                <?php 
                    if($phone) {
                        echo '<li><a href = "'.$phone.'"><i class="fa fa-phone-square" aria-hidden="true"></i> Phone</a></li>';
                    }
                ?>
                
            </ul>

            <h4>Our Social Media</h4>

           <?php
$social_icons = [
    'facebook'  => 'fa-facebook',
    'instagram' => 'fa-instagram',
    'x'         => 'fa-twitter', // fallback for twitter/x
    'gmail'     => 'fa-google'
];

$social_media = get_field('social_media');
echo '<div class = "social-media-follow">';
if ( $social_media ) {

    foreach ( $social_media as $social ) {

        // normalize value
        $platform = strtolower($social['social_media_account']);
        $url      = $social['url'];

        if ( isset($social_icons[$platform]) ) {

            echo '<a href="' . esc_url($url) . '" target="_blank">';
            echo '<i class="fab ' . esc_attr($social_icons[$platform]) . '"></i>';
            echo '</a>';

        }

    }

}
echo '</div>';
?>
          

    </div>
  

</div>
