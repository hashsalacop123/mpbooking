<div class = "sidebar-wrapper">

<?php 
// sidebar dashbarod
$user = wp_get_current_user();

if ( in_array( 'player', (array) $user->roles ) ) {
    echo '<p>This is for Players</p>';

} elseif ( in_array( 'coach', (array) $user->roles ) ) { ?>
<?php 

/**
 * Get the coach post of the current logged-in user
 */
$coach = get_posts([
    'post_type'   => 'coach',
    'author'      => get_current_user_id(),
    'numberposts' => 1
]);


?>

 <ul>
        <li><a href = "/dashboard/"><i class="fa fa-volume-up"></i> Dashboard</a></li>
        <li><a href = "/my-account/"><i class="fa fa-user-circle" aria-hidden="true"></i> Information</a></li>
                 <li><a href = "/review/"><i class="fa-regular fa-address-card"></i> Your Review</a></li>

        <?php 
            if ( ! empty($coach) ) {

            echo '<li><a href = "'.get_permalink($coach[0]->ID).'"><i class="fa fa-eye" aria-hidden="true"></i>
 View Profile</a></li>';
               
            }
        ?>
                <li><a href = "/update/"><i class="far fa-edit"></i> Profile</a></li>

        <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link">
               <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
            </a>
        </li>   
 </ul>

<?php } elseif ( in_array( 'court', (array) $user->roles ) ) { ?>

 <ul>
        <li><a href = "/dashboard/"><i class="fa fa-volume-up" aria-hidden="true"></i> Dashboard</a></li>
        <li><a href = "/my-account/"><i class="fa fa-user-circle" aria-hidden="true"></i> My Account</a></li>
         <li><a href = "/dashboard/review/"><i class="fa-regular fa-address-card"></i> Your Review</a></li>
        <li><a href = "/dashboard/create-update/"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create + Update</a></li>
        <li><a href = "/dashboard/court-name-schedule/"><i class="fa fa-calendar" aria-hidden="true"></i>  Add/Update Schedule</a></li>

        <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>Logout
            </a>
        </li>   
 </ul>

<?php } elseif (in_array('administrator',(array) $user->roles)) { ?>
     <ul>
        <li><a href = "/dashboard/admin"><i class="fa fa-volume-up" aria-hidden="true"></i> Dashboard</a></li>
        <li><a href = "/dashboard/my-account/"><i class="fa fa-user-circle" aria-hidden="true"></i> My Account</a></li>
        <li><a href = "/dashboard/coach/"><i class="fa fa-users" aria-hidden="true"></i></i> Coach</a></li>
        <li><a href = "/dashboard/court/"><i class="fa fa-trophy" aria-hidden="true"></i></i> Court</a></li>

        <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>Logout
            </a>
        </li>   
 </ul>
<?php } ?>


   
</div>