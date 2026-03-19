<?php
// Template Name: Update
acf_form_head(); // Must be first
get_header();
?>
<style>
    #acf-field_6974b643e4c4d {
        display: none!important;
    }

</style>
<div class="dasboard-wrapper-page">
    <div class="container">
<div class = "row">
    <!-- SIDEBAR START HERE -->
    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
    </div>
    <!-- CONTTENT START HERE -->
    <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-12">
    <?php 

$user = wp_get_current_user();

if ( in_array( 'player', (array) $user->roles ) ) {
    echo '<p>This is for Players</p>';

} elseif ( in_array( 'coach', (array) $user->roles ) ) {

$current_user = wp_get_current_user();

// 1. Check if a post ID is explicitly passed (editing)
$coach_post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

// 2. If no ID, get the first coach post by this user
if (!$coach_post_id) {
    $args = array(
        'post_type'   => 'coach',
        'author'      => $current_user->ID,
        'post_status' => array('publish','draft'),
        'numberposts' => 1,
    );

    $coach_posts = get_posts($args);
    $coach_post_id = !empty($coach_posts) ? $coach_posts[0]->ID : 0;
}

// 3. If still no post, mark as new
if (!$coach_post_id) {
    $coach_post_id = 'new_post';
}

// ----------------------------------------
// Localize the availability calendar JS
// ----------------------------------------
// Get coach bookings
$coach_id = $current_user->ID;

$args = [
    'post_type' => 'booking',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => 'coach__services',
            'value' => $coach_id,
            'compare' => '='
        ],
        [
            'key' => 'booking_status',
            'value' => ['pending', 'approved'],
            'compare' => 'IN'
        ]
    ]
];

$booking_posts = get_posts($args);

$booking_events = [];

foreach ($booking_posts as $booking) {
    $date   = get_field('date_booked', $booking->ID);
    $start  = get_field('time_start', $booking->ID);
    $end    = get_field('time_end', $booking->ID);
    $status = get_field('booking_status', $booking->ID);

    if (!$date || !$start || !$end) continue;

    $start_dt = date('Y-m-d\TH:i:s', strtotime("$date $start"));
    $end_dt   = date('Y-m-d\TH:i:s', strtotime("$date $end"));

    $booking_events[] = [
        'start' => $start_dt,
        'end'   => $end_dt,
        'title' => ucfirst($status),
        'color' => ($status === 'pending') ? 'yellow' : '#888'
    ];
}

// Localize both availability + booking data
wp_localize_script('availability-calendar','availabilityData',[
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('availability_nonce'),
    'post_id' => $coach_post_id,
    'field_key' => 'field_6974b643e4c4d',
    'booking_events' => $booking_events
]);


// ----------------------------------------
// Display ACF form
// ----------------------------------------

?>
<!-- Bootstrap Tabs -->
 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#calendarModal">
  Manage Availability Calendar
</button>

<div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document"> <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Set Your Availability</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="availability-calendar-frontend"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Done / Close</button>
      </div>
    </div>
  </div>
</div>

    <!-- <div id="availability-calendar-frontend" style="height:500px;"></div> -->


<?php
// If post exists, make sure current user is the author
if ($coach_post_id && $coach_post_id !== 'new_post') {
    wp_update_post([
        'ID' => $coach_post_id,
        'post_author' => get_current_user_id(),
    ]);
}


acf_form(array(
    'post_id' => $coach_post_id,
    'new_post' => $coach_post_id === 'new_post' ? array(
        'post_type'   => 'coach',
        'post_status' => 'publish',
        'post_author' => $current_user->ID,
        'post_title'  => $current_user->display_name . ' Coach Profile',
    ) : false,
    'fields' => array(
        'field_6974b618e4c4b', // Profile Photo
        'field_6974b415e4c46', // Nick Name
        'field_6974b662e4c4e', // About Me
        'field_6985aadcef579', // sports
        'field_6974b453e4c47', // Gender
        'field_6974b490e4c48', // Address
        'field_69869b621ebe4', // City
        'field_6974b4a8e4c49', // Phone
        'field_6974b5e7e4c4a', // Hourly Rate
        'field_6974b630e4c4c', // hourly rate
        'field_6974b643e4c4d', // availability
    ),
    'submit_value' => $coach_post_id === 'new_post' ? 'Create Profile' : 'Update Profile',
    'return' => add_query_arg('updated', 'true', get_permalink()),
    'html_form' => true,
)); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if the URL contains "updated=true"
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('updated') === 'true') {
        Swal.fire({
            title: 'Success!',
            text: 'Your coach profile has been saved successfully.',
            icon: 'success',
            confirmButtonText: 'Great!',
            confirmButtonColor: '#3085d6',
            // Optional: Clean up the URL after showing the alert
            didClose: () => {
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        });
    }
});
</script>

<?php } elseif ( in_array( 'court', (array) $user->roles ) ) {
        // Get current logged-in user
 


$current_user_id = get_current_user_id();

if ($current_user_id) {

    // Query services of current user
    $args = array(
        'post_type'      => 'service',
        'author'         => $current_user_id,
        'post_status'    => 'publish',
        'posts_per_page' => -1
    );

    $user_services = new WP_Query($args);

    if ($user_services->have_posts()) {

        echo '<ul class="user-services">';

        while ($user_services->have_posts()) {

            $user_services->the_post();

            $address = get_field('address');

            echo '<li>';
            echo '<a href="' . site_url('/dashboard/update-services?post_id=' . get_the_ID()) . '">
                    <i class="fa fa-pencil-square"></i> 
                    ' . get_the_title() . ' - ' . $address . '
                  </a>';
            echo '</li>';

        }

        echo '</ul>';

        wp_reset_postdata();

    } else {

        echo '<p>You have not posted any services yet.</p>';

    }

} else {

    echo '<p>Please log in to view your services.</p>';

}

}





    
    ?>
     </div>
</div>

    </div>
</div>

<?php get_footer(); ?>