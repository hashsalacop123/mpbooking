        <div class = "header-wrapper">
            <div class = "container">
            <div class = "row align-items-center">
                 <div class = "col-sm-12 col-md-6">
                    <div class = "logo">
                       <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src = "<?php echo get_stylesheet_directory_uri(); ?>/img/match-point-logo-transparent.png" class = "img-fluid"></a>
                    </div>
                 </div>
                <div class = "col-sm-12 col-md-6 ">
                    <div class = "left-menu-header">
                          <?php if ( is_user_logged_in() ) : ?>
                            <!-- Show Dashboard if logged in -->
                            <ul>
                                <li>
                                    <a href="/dashboard/" class="button-general">
                                        Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo wp_logout_url( home_url() ); ?>" class="button-general logout-link">
                                        Logout <i class="fas fa-sign-out-alt" aria-hidden="true"></i>

                                    </a>
                                </li>
                            </ul>
                        <?php else : ?>
                        <ul>
                            <li><a href = "/registration/" class = "button-general">Be a Member <i class="fa fa-user" aria-hidden="true"></i></a></li>
                            <li><a href = "/login" class = "button-general">Login <i class="fa fa-sign-in" aria-hidden="true"></i></a></li>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
              
                </div>
            </div>
        </div>