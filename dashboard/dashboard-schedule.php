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
    ?>
    <div class="dasboard-wrapper-page">
        <div class="container">
            <div class="row">
                <aside class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
                </aside>
                <section class="col-xl-9 col-lg-9 col-md-8 col-sm-12 mp-dashboard-content">
                    <div class="mp-dashboard-page-heading">
                        <div><p class="mp-dashboard-eyebrow">Court management</p><h1>Court schedule</h1><p>Set your venue up before adding its schedule.</p></div>
                    </div>
                    <div class="mp-dashboard-card mp-dashboard-empty">
                        <span class="mp-dashboard-empty__icon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                        <h2>Create your court profile first</h2>
                        <p>Your schedule will be connected to your court listing.</p>
                        <a href="<?php echo esc_url(home_url('/dashboard/create-update/')); ?>" class="mp-dashboard-primary-link">Create court profile</a>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php
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
    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 mp-dashboard-content">

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
        <div class="mp-dashboard-page-heading">
            <div>
                <p class="mp-dashboard-eyebrow">Court management</p>
                <h1>Court schedule</h1>
                <p>Manage your courts and the times players can book them.</p>
            </div>
            <?php if (!empty($service)) : ?>
                <a href="<?php echo esc_url(get_permalink($service[0]->ID)); ?>" target="_blank" rel="noopener" class="mp-dashboard-primary-link">View public court</a>
            <?php endif; ?>
        </div>

        <?php
        /*
        |--------------------------------------------------------------------------
        | ACF FORM (REPEATER: COURTS)
        |--------------------------------------------------------------------------
        */
        echo '<div class="mp-dashboard-card mp-dashboard-form-card">';
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
        echo '</div>';
        ?>

    </div>
<script>
jQuery(document).ready(function($) {

    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('updated') === 'true') {
        Swal.fire({
            icon: 'success',
            title: 'Saved!',
            text: 'Court schedule has been updated.',
            confirmButtonText: 'OK'
        }).then(() => {
            // remove query param without reloading
            const newUrl = window.location.pathname + window.location.hash;
            window.history.replaceState({}, document.title, newUrl);
        });
    }

});
</script>
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
