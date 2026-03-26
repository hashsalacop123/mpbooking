<?php
// Template Name: Update services

acf_form_head();
get_header();

$current_user = wp_get_current_user();

/*
|--------------------------------------------------------------------------
| Get Post ID From URL
|--------------------------------------------------------------------------
*/
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

/*
|--------------------------------------------------------------------------
| Security Check
|--------------------------------------------------------------------------
| Make sure the current user owns the service
*/
if (!$post_id || get_post_type($post_id) !== 'service') {
    echo '<p>Invalid service.</p>';
    get_footer();
    return;
}

if (get_post_field('post_author', $post_id) != get_current_user_id()) {
    echo '<p>You are not allowed to edit this service.</p>';
    get_footer();
    return;
}

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
#acf-field_69b53dfc0976b{
display:none!important;
}
.acf-field-69b53dfc0976b {
    display:none;
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

<!-- Availability Button -->
 <div class = "button-data">
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#calendarModal">
Manage Availability Calendar
</button>

<?php 
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
?>
<a href="<?php echo get_permalink($post_id); ?>" target="_blank" class="btn btn-primary">
    View Your Profile
</a>
</div>

<!-- Calendar Modal -->
<div class="modal fade" id="calendarModal">
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

<!-- Map -->
<div id="geocoder" style="margin-bottom:10px;"></div>
<div id="map" style="height:300px;margin-bottom:10px;"></div>

<?php

/*
|--------------------------------------------------------------------------
| ACF Update Form
|--------------------------------------------------------------------------
*/

acf_form([
'post_id' => $post_id,

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

'submit_value' => 'Update Service',

'return' => add_query_arg([
'post_id' => $post_id,
'updated' => 'true'
], get_permalink()),

'html_form' => true
]);

?>

<input type="hidden" id="acf-field_lat" name="acf[field_lat]" />
<input type="hidden" id="acf-field_lng" name="acf[field_lng]" />

</div>
</div>
</div>
</div>

<?php get_footer(); ?>