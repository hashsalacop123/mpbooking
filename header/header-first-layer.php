<div class = "first-layer-wrapper">
    <div class = "container">
        <div class = "row align-items-center">
            <div class = "col-lx-6 col-lg-6 col-md-12 col-xs-12">

<div class = "user-wrapper">
    <?php $user = get_current_user_id();
        $user = get_userdata( $user );
      if ($user) : ?>
    <a href = "#">Hi, <span><?php echo $user->display_name; ?></span></a>
    <?php endif; ?>
</div>            </div>
            <div class = "col-lx-6 col-lg-6 col-md-12 col-xs-12">
                <div class = "left-menu-header">
          <?php if ( is_user_logged_in() ) : ?>
    <!-- Show Dashboard if logged in -->
    <ul>
        <li>
            <?php 
                   
                    if ( current_user_can('administrator') ) { ?>

                          <a href="/dashboard/admin" class="button-general">
                                    Admin Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i>
                                </a>

                    <?php } else { ?>

                          <a href="/dashboard/" class="button-general ">
                            Dashboard <i class="fa fa-tachometer" aria-hidden="true"></i>
                        </a>

                 <?php   } ?>
          
        </li>
          <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link button-general">
                Logout <i class="fas fa-sign-out-alt" aria-hidden="true"></i>

            </a>
        </li>
    </ul>
<?php else : ?>
    <!-- Show Register & Login if not logged in -->
    <ul>
        <li>
            <a href="/registration/" class="button-general">
                Be a Member <i class="fa fa-user" aria-hidden="true"></i>
            </a>
        </li>
        <li>
            <a href="/login" class="button-general" class = "logout-link">
                Login <i class="fa fa-sign-in" aria-hidden="true"></i>
            </a>
        </li>
    </ul>
<?php endif; ?>
                </div>            
            </div>
        </div>
    </div>
</div>