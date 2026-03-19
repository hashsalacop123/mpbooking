
<?php            get_template_part( 'header/header-first-layer' ); ?>

<div class = "header-main-wrapper-inner">
                 <!-- THIS HEADER IS FOR THE NORMAL PAGE -->
            <div class = "container header-wrapper-standard">
                <div class = "row align-items-center">
                    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-5">
                        <div class = "inner-page-logo">
                             <a href = "<?php echo esc_url( home_url( '/' ) ); ?>"><img src = "<?php echo get_stylesheet_directory_uri(); ?>/img/match-point-logo-transparent.png" class = "img-fluid"></a>
                        </div>
                    
                     </div>
                     <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-7">
                        <div class = "navigation-wrapper deskotop-menu">
                            <nav>
                            <ul>
                                <li><a href = "<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                                <li><a href = "/court/">Court</a></li>
                                <li><a href = "/coaches/">Coaches</a></li>
                                <li><a href = "/gallery/">Gallery</a></li>
                                <li><a href = "/about-us/">About Us</a></li>
                                <li><a href = "/contact-us/">Contact us</a></li>

                            </ul>
                            </nav>
                        </div>
                        <div id="navigator">
                            <ul id="nav">
                            
                                <li class="nav_tab"><a href = "<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                                <li class="nav_tab"><a href = "/court/">Court</a></li>
                                <li class="nav_tab"><a href = "/coaches/">Coaches</a></li>
                                <li class="nav_tab"><a href = "/gallery/">Gallery</a></li>
                                <li class="nav_tab"><a href = "/about-us/">About Us</a></li>
                                <li class="nav_tab"><a href = "/contact-us/">Contact us</a></li>
                            </ul>
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