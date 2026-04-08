<footer>
    <div class = "footer-wrapper inner-footer">
        <div class = "container">
                <div class="row">
                    <div class = "col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class = "footer-first-column footer-column">

                        <?php 
                            $heading_footer = get_field('heading_footer','option');
                            $about_text_footer = get_field('about_text_footer','option');
                            $heading_column_2 = get_field('heading_column_2','option');
                            $heading_column_3 = get_field('heading_column_3','option');
                            $heading_column_4 = get_field('heading_column_4','option');
                            if($heading_footer) :
                                echo '<h5>'.esc_attr($heading_footer).'</h5>';
                            endif;

                            if($about_text_footer) :
                                echo wp_kses_post($about_text_footer);
                            endif;
                        ?>
                        </div>
                    </div>
                    <div class = "col-xl-2 col-lg-2 col-md-2 col-sm-12">
                        <div class = "footer-second-column footer-column">
                          
                            <?php 
                                if($heading_column_2) :
                                    echo '<h5>'.esc_attr($heading_column_2).'</h5>';
                                endif;

            
                                $menu_locations = get_nav_menu_locations();
                                $menu_id = $menu_locations['footer_one'];
                                $menu_items = wp_get_nav_menu_items($menu_id);

                                if ($menu_items) {
                                    echo '<ul>';

                                    foreach ($menu_items as $item) {
                                        echo '<li>';
                                        echo '<a href="' . esc_url($item->url) . '">';
                                        echo esc_html($item->title);
                                        echo '</a>';
                                        echo '</li>';
                                    }

                                    echo '</ul>';
                                }
                            ?>
                              
            
                         
                        </div>
                    </div>
                    <div class = "col-xl-2 col-lg-2 col-md-2 col-sm-12">
                        <div class = "footer-third-column footer-column">
                               <?php 
                                if($heading_column_3) :
                                    echo '<h5>'.esc_attr($heading_column_3).'</h5>';
                                endif;

                                   $menu_locations = get_nav_menu_locations();
                                $menu_id = $menu_locations['footer_two'];
                                $menu_items = wp_get_nav_menu_items($menu_id);

                                if ($menu_items) {
                                    echo '<ul class = "footer-third-new">';

                                    foreach ($menu_items as $item) {
                                        echo '<li>';
                                        echo '<a href="' . esc_url($item->url) . '">';
                                        echo esc_html($item->title);
                                        echo '</a>';
                                        echo '</li>';
                                    }

                                    echo '</ul>';
                                }
                             ?>
                           
                           
                        </div>
                    </div>
                    <div class = "col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class = "footer-third-column footer-column">
                            <?php 
                              
                                if($heading_column_4) :
                                    echo '<h5>'.esc_attr($heading_column_4).'</h5>';
                                endif;
                            
                             $subscribe = get_field('subsriptions', 'option');
                                if (!empty($subscribe)) {
                                    echo do_shortcode($subscribe);
                                }

                                $facebook = get_field('facebook','option');
                                $youtube = get_field('youtube','option');
                                $instagram = get_field('instagram','option');
                                $tiktok = get_field('tiktok','option');
                            ?>
                             <h5>Follow Us</h5>
                                <ul class = "social-media">
                                    <?php if($facebook) : 
                                            echo '<li><a href = "'.esc_attr($facebook).'"  target = "_blank"><i class="fa-brands fa-facebook"></i></a></li>';
                                        endif;
                                        if($youtube) : 
                                            echo '<li><a href = "'.esc_attr($youtube).'"  target = "_blank"><i class="fa-brands fa-youtube"></i></a></li>';
                                        endif;

                                        if($instagram) : 
                                            echo '<li><a href = "'.esc_attr($instagram).'"  target = "_blank"><i class="fa-brands fa-square-instagram"></i></a></li>';
                                        endif;
                                        
                                           if($tiktok) : 
                                            echo '<li><a href = "'.esc_attr($tiktok).'"  target = "_blank"><i class="fa-brands fa-tiktok"></i></a></li>';
                                        endif;
                                    ?>
                                </ul>                            
         
                        </div>
                        
                    </div>
                 <div class = "col-md-12">
                            <hr>
                            <?php 
                                $contact_number = get_field('contact_number','option');
                                $address = get_field('address','option');
                                $email = get_field('email','option');
                            ?>
                             <ul class = "contact-footer">
                                        <?php 
                                            if($contact_number) :
                                                echo '<li><i class="fas fa-phone"></i>
                                                      <a href = "tel:+'.$contact_number.'">'.$contact_number.'</a>
                                                     </li>';
                                            endif;

                                            if($contact_number) :
                                                echo '<li><i class="fa-solid fa-map-pin"></i>'.$address.'</li>';
                                            endif;

                                             if($email) :
                                                echo '<li><i class="fa fa-envelope" aria-hidden="true"></i>'.$email.'</li>';
                                            endif;
                                        ?>
                        </ul>
                        </div>
                </div>
        </div>
    </div>
    <div class = "bottom-container">
        <div class = "container">
            <div class = "row">
                
                    <div class = "col-sm-12 col-md-12">
                        <div class = "bottom-ft-match">
                            <?php 
                                $copy_right_bottom_text = get_field('copy_right_bottom_text','option');

                                if($copy_right_bottom_text) :
                                    echo '<p class = "copy-right-bottom-text">'.$copy_right_bottom_text.'</p>';
                                endif;
                            ?>
                     
                        </div>
                    </div>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

