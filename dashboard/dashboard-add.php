<?php
// TEMPLATE NAME: Add

acf_form_head(); // Must be first
get_header();

$current_user = wp_get_current_user();

/*
|--------------------------------------------------------------------------
| Get User Name
|--------------------------------------------------------------------------
*/
$full_name = trim($current_user->first_name . ' ' . $current_user->last_name);

if ( empty($full_name) ) {
    $full_name = $current_user->display_name;
}

/*
|--------------------------------------------------------------------------
| Post ID
|--------------------------------------------------------------------------
| Let ACF create the post
*/
$post_id = 'new_post';

/*
|--------------------------------------------------------------------------
| Calendar Config
|--------------------------------------------------------------------------
*/
wp_localize_script('availability-calendar', 'availabilityData', [
    'ajax_url'  => admin_url('admin-ajax.php'),
    'nonce'     => wp_create_nonce('availability_nonce'),
    'post_id'   => $post_id,
    'field_key' => 'field_69b53dfc0976b', // availability
    'booking_events' => []
]);

?>

<style>
/*
|--------------------------------------------------------------------------
| Hide raw ACF availability field
|--------------------------------------------------------------------------
*/
#acf-field_69b53dfc0976b{
    display:none!important;
}
</style>

<div class="dasboard-wrapper-page">
<div class="container">
<div class="row">
<?php hash_show_pending_registration_notice(); ?>
<!-- SIDEBAR -->
<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
<?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
</div>

<!-- CONTENT -->
<div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">

<!-- Availability Button -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#calendarModal">
Manage Availability Calendar
</button>
<!-- Calendar Modal -->
<div class="modal fade" id="calendarModal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-xl" role="document">
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

<!-- Map Search -->
<div id="geocoder" style="margin-bottom:10px;"></div>

<!-- Map -->
<div id="map" style="height:300px;margin-bottom:10px;"></div>

<?php

/*
|--------------------------------------------------------------------------
| ACF FRONTEND FORM
|--------------------------------------------------------------------------
*/

acf_form([
'post_id'  => 'new_post',

'new_post' => [
'post_type'   => 'service',
'post_status' => 'publish',
'post_author' => $current_user->ID,
'post_title'  => $full_name
],

'fields' => [
'field_69b89e113985a', // courth name
'field_695342a608250', // address

'field_694f8fb13c33e', // are_you

'field_695372c113ba9', // address_lat
'field_695372d913baa', // address_lng

'field_694f91a9337a4', // featured_image

'field_695a06f665038', // HOURLY RATE

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

'submit_value' => 'Submit',

'return' => add_query_arg(
    'submitted',
    'true',
    get_permalink()
),

'html_form' => true
]);

?>

<!-- Hidden fields for map coordinates -->
<input type="hidden" id="acf-field_lat" name="acf[field_lat]" />
<input type="hidden" id="acf-field_lng" name="acf[field_lng]" />

</div>
</div>
</div>
</div>

<?php get_footer(); ?>