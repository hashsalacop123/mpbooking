<?php
// Template Name: Service Form (Create + Update)

acf_form_head();
get_header();

$current_user = wp_get_current_user();

/*
|--------------------------------------------------------------------------
| Get User Name
|--------------------------------------------------------------------------
*/
$full_name = trim($current_user->first_name . ' ' . $current_user->last_name);

if (empty($full_name)) {
    $full_name = $current_user->display_name;
}

/*
|--------------------------------------------------------------------------
| Detect Existing Service Post
|--------------------------------------------------------------------------
*/
$args = [
    'post_type'      => 'service',
    'author'         => get_current_user_id(),
    'posts_per_page' => 1,
    'post_status'    => ['publish', 'draft', 'pending']
];

$existing_post = get_posts($args);

/*
|--------------------------------------------------------------------------
| Determine Mode (Create or Update)
|--------------------------------------------------------------------------
*/
$post_id = !empty($existing_post) ? $existing_post[0]->ID : 'new_post';

/*
|--------------------------------------------------------------------------
| Security Check (only if editing)
|--------------------------------------------------------------------------
*/
if ($post_id !== 'new_post' && get_post_field('post_author', $post_id) != get_current_user_id()) {
    wp_die('You are not allowed to edit this service.');
}

/*
|--------------------------------------------------------------------------
| Calendar Config
|--------------------------------------------------------------------------
*/
wp_localize_script('availability-calendar', 'availabilityData', [
    'ajax_url'  => admin_url('admin-ajax.php'),
    'nonce'     => wp_create_nonce('availability_nonce'),
    'post_id'   => $post_id === 'new_post' ? 0 : $post_id,
    'field_key' => 'field_69b53dfc0976b',
    'booking_events' => []
]);
?>

<style>
/*
|--------------------------------------------------------------------------
| Hide raw ACF availability field
|--------------------------------------------------------------------------
*/
#acf-field_69b53dfc0976b,
.acf-field-69b53dfc0976b {
    display: none !important;
}
</style>

<div class="dasboard-wrapper-page">
<div class="container">
<div class="row">

<!-- SIDEBAR -->
<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
<?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
</div>

<!-- CONTENT -->
<div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">

<!-- ACTION BUTTONS -->
<div class="button-data" style="margin-bottom:15px;">
    
    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#calendarModal">
        Manage Availability Calendar
    </button> -->

    <?php if ($post_id !== 'new_post'): ?>
        <a href="<?php echo get_permalink($post_id); ?>" target="_blank" class="btn btn-primary">
            View Your Profile
        </a>
    <?php endif; ?>

</div>

<!-- CALENDAR MODAL -->
<div class="modal fade" id="calendarModal" tabindex="-1">
<div class="modal-dialog modal-xl">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Set Your Availability</h5>
<button type="button" class="close" data-dismiss="modal">
<span>&times;</span>
</button>
</div>

<div class="modal-body">
<div id="availability-calendar-frontend"></div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-primary" data-dismiss="modal">
Done / Close
</button>
</div>

</div>
</div>
</div>

<!-- MAP SEARCH -->
<div id="geocoder" style="margin-bottom:10px;"></div>

<!-- MAP -->
<div id="map" style="height:300px;margin-bottom:10px;"></div>

<?php
/*
|--------------------------------------------------------------------------
| ACF FORM (CREATE + UPDATE)
|--------------------------------------------------------------------------
*/
acf_form([

    'post_id' => $post_id,

    /*
    |--------------------------------------------------------------------------
    | Only used when creating
    |--------------------------------------------------------------------------
    */
    'new_post' => [
        'post_type'   => 'service',
        'post_status' => 'publish',
        'post_author' => $current_user->ID,
        'post_title'  => $full_name
    ],

    /*
    |--------------------------------------------------------------------------
    | Fields
    |--------------------------------------------------------------------------
    */
    'fields' => [
        'field_69b89e113985a', // court name
        'field_695342a608250', // address

        'field_694f8fb13c33e', // are_you

        'field_695372c113ba9', // address_lat
        'field_695372d913baa', // address_lng

        'field_694f91a9337a4', // featured_image

        'field_695a06f665038', // hourly rate

        'field_694f8e7f3c336', // age
        'field_694f90df260bf', // gender

        'field_694f91c4337a5', // phone
        'field_6959ed3caa0db', // email
        'field_6959ee0892a89', // website

        'field_694f8e8c3c337', // images
        'field_694f8ea43c338', // social_media

        'field_69b53dfc0976b', // availability

        'field_694f8f923c33d', // about_me
        'field_694f90873c33f', // additional_information
    ],

    /*
    |--------------------------------------------------------------------------
    | Button Text Dynamic
    |--------------------------------------------------------------------------
    */
    'submit_value' => $post_id === 'new_post' ? 'Submit' : 'Update Service',

    /*
    |--------------------------------------------------------------------------
    | Redirect After Submit
    |--------------------------------------------------------------------------
    */
    'return' => add_query_arg([
        'updated' => 'true'
    ], get_permalink()),

    'html_form' => true

]);
?>

<!-- Hidden fields for map -->
<input type="hidden" id="acf-field_lat" name="acf[field_lat]" />
<input type="hidden" id="acf-field_lng" name="acf[field_lng]" />

</div>
</div>
</div>
</div>

<?php get_footer(); ?>