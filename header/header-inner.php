
<?php            get_template_part( 'header/header-first-layer' ); ?>

<div class = "header-main-wrapper-inner">
                 <!-- THIS HEADER IS FOR THE NORMAL PAGE -->
            <div class = "container header-wrapper-standard">
                <div class = "row align-items-center">
                    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-5">
                        <div class = "inner-page-logo">
                            <?php 
                                $logo = get_field('logo','option');
                                if($logo) : 
                                    echo '<a href = "'.esc_url(home_url('/')).'"><img src = "'.$logo['url'].'" alt = "'.$logo['alt'].'" class = "img-fluid">';
                                endif;
                            ?>
                        </div>
                    
                     </div>
                     <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-7">
                        <div class = "navigation-wrapper deskotop-menu">
                            <nav>
                        <?php 

                               $menu_locations = get_nav_menu_locations();
                                $menu_id = $menu_locations['header_menu'];
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
                            </nav>
                        </div>
                        <div id="navigator">
                                   <?php 

                               $menu_locations = get_nav_menu_locations();
                                $menu_id = $menu_locations['header_menu'];
                                $menu_items = wp_get_nav_menu_items($menu_id);

                                if ($menu_items) {
                                    echo '<ul id="nav">';

                                    foreach ($menu_items as $item) {
                                        echo '<li class="nav_tab">';
                                        echo '<a href="' . esc_url($item->url) . '">';
                                        echo esc_html($item->title);
                                        echo '</a>';
                                        echo '</li>';
                                    }

                                    echo '</ul>';
                                }
                                ?>
                       
                        </div>
                        
                        <div class="menu-icon">			
                            <div class="line_one"></div>
                            <div class="line_two"></div>
                            <div class="line_three"></div>  			
                        </div>
                        </div>
                    </div>
                </div>
            </div>