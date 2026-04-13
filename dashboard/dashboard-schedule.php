<?php
/**
 * Template Name: Court Schedule
 */

acf_form_head();
get_header();

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/
$current_user = wp_get_current_user();
$user_id      = get_current_user_id();

/*
|--------------------------------------------------------------------------
| Get Service Post
|--------------------------------------------------------------------------
*/
function mp_get_user_service_post($user_id) {

    $args = [
        'post_type'      => 'service',
        'author'         => $user_id,
        'posts_per_page' => 1,
        'post_status'    => ['publish', 'draft', 'pending']
    ];

    $posts = get_posts($args);

    return !empty($posts) ? $posts[0]->ID : false;
}

$post_id = mp_get_user_service_post($user_id);

/*
|--------------------------------------------------------------------------
| Security
|--------------------------------------------------------------------------
*/
if (!$post_id) {
    echo '<p>No service found.</p>';
    get_footer();
    return;
}

if (get_post_field('post_author', $post_id) != $user_id) {
    wp_die('You are not allowed to access this page.');
}
?>

<style>
/*
|--------------------------------------------------------------------------
| Hide raw JSON field
|--------------------------------------------------------------------------
*/
.acf-field[data-name="court_calendar"] {
    display: none !important;
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

        <?php
        /*
        |--------------------------------------------------------------------------
        | Header / Title
        |--------------------------------------------------------------------------
        */
        $service = get_posts([
    'post_type'   => 'service',
    'author'      => get_current_user_id(),
    'numberposts' => 1
]);

// echo '<pre>';
//     var_dump(get_permalink($service[0]->ID));
// echo '</pre>';

        ?>
        <div class="mb-3 schedule-wrapper">
            <h3>Manage Court Schedule</h3>

            <p>Add courts and set availability for each one.</p>
                     <a href = "<?php echo get_permalink($service[0]->ID); ?>" target = "_blank" class ="main-btn-general">View your Court</a>

        </div>

        <?php
        /*
        |--------------------------------------------------------------------------
        | ACF FORM (REPEATER: COURTS)
        |--------------------------------------------------------------------------
        */
        acf_form([
            'post_id' => $post_id,

            'fields' => [
                'field_69c0a09fe11f0', // 🔁 replace with your actual repeater key
            ],

            'submit_value' => 'Save Court Schedule',

            'return' => add_query_arg([
                'updated' => 'true'
            ], get_permalink()),

            'html_form' => true
        ]);
        ?>

    </div>

</div>
</div>
</div>

<?php
/*
|--------------------------------------------------------------------------
| Calendar Modal (GLOBAL - reused)
|--------------------------------------------------------------------------
*/
?>
<div class="modal fade" id="calendarModal" tabindex="-1">
<div class="modal-dialog modal-xl">
<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title">Court Availability</h5>
        <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
        </button>
    </div>

    <div class="modal-body">
        <div id="availability-calendar-frontend"></div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveCalendar">
            Save Calendar
        </button>
    </div>

</div>
</div>
</div>




<?php get_footer(); ?>