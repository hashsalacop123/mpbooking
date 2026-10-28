<?php
/**
 * Coach profile and availability editor for /dashboard/update/.
 * ACF form head is initialized by the parent dashboard template.
 */

$current_user = wp_get_current_user();
$coach_posts = get_posts([
    'post_type'      => 'coach',
    'author'         => $current_user->ID,
    'posts_per_page' => 1,
    'post_status'    => ['publish', 'draft', 'pending', 'private'],
    'orderby'        => 'date',
    'order'          => 'ASC',
]);

$coach_post = $coach_posts ? $coach_posts[0] : null;
$coach_post_id = $coach_post ? $coach_post->ID : 'new_post';
$display_name = trim($current_user->first_name . ' ' . $current_user->last_name);
if (!$display_name) {
    $display_name = $current_user->display_name;
}

$booking_events = [];
$booking_posts = get_posts([
    'post_type'      => 'booking',
    'posts_per_page' => -1,
    'post_status'    => ['publish', 'private'],
    'meta_query'     => [[
        'key'     => 'coach__services',
        'value'   => (string) $current_user->ID,
        'compare' => '=',
    ]],
]);

$manila_timezone = new DateTimeZone('Asia/Manila');
foreach ($booking_posts as $booking_post) {
    $status = (string) get_field('booking_status', $booking_post->ID);
    if (!in_array($status, ['pending', 'approved'], true)) {
        continue;
    }

    if ($status === 'pending') {
        $hold_expires = get_field('hold_expires', $booking_post->ID);
        if ($hold_expires && strtotime($hold_expires) <= current_time('timestamp')) {
            continue;
        }
    }

    $date = get_field('date_booked', $booking_post->ID);
    $start = get_field('time_start', $booking_post->ID);
    $end = get_field('time_end', $booking_post->ID);
    if (!$date || !$start || !$end) {
        continue;
    }

    try {
        $start_datetime = new DateTime($date . ' ' . $start, $manila_timezone);
        $end_datetime = new DateTime($date . ' ' . $end, $manila_timezone);
    } catch (Exception $exception) {
        continue;
    }

    $booking_events[] = [
        'title'           => $status === 'approved' ? 'Booked' : 'Booking pending',
        'start'           => $start_datetime->format(DateTime::ATOM),
        'end'             => $end_datetime->format(DateTime::ATOM),
        'editable'        => false,
        'startEditable'   => false,
        'durationEditable'=> false,
        'backgroundColor' => $status === 'approved' ? '#d94b55' : '#d89a2b',
        'borderColor'     => $status === 'approved' ? '#d94b55' : '#d89a2b',
        'extendedProps'   => [
            'isBooking' => true,
            'status'    => $status,
        ],
    ];
}

$coach_availability_data = [
    'ajax_url'       => admin_url('admin-ajax.php'),
    'nonce'          => wp_create_nonce('availability_nonce'),
    'post_id'        => $coach_post ? $coach_post->ID : 0,
    'field_key'      => 'field_6974b643e4c4d',
    'booking_events' => $booking_events,
];

get_header();
?>

<script>
window.availabilityData = <?php echo wp_json_encode($coach_availability_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>

<main class="mp-coach-dashboard dasboard-wrapper-page">
    <div class="container">
        <?php hash_show_pending_registration_notice(); ?>
        <div class="row">
            <aside class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
            </aside>

            <section class="col-xl-9 col-lg-9 col-md-8 col-sm-12 mp-dashboard-content">
                <div class="mp-coach-dashboard__heading">
                    <div>
                        <p class="mp-coach-dashboard__eyebrow">Coach account</p>
                        <h1><?php echo $coach_post ? 'Edit your coach profile' : 'Create your coach profile'; ?></h1>
                        <p>Keep your coaching details current and set the times players can book.</p>
                    </div>
                    <?php if ($coach_post) : ?>
                        <a class="btn btn-outline-primary" href="<?php echo esc_url(get_permalink($coach_post->ID)); ?>" target="_blank" rel="noopener">
                            View public profile
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['updated']) && 'true' === sanitize_text_field(wp_unslash($_GET['updated']))) : ?>
                    <div class="alert alert-success" role="status">Your coach profile and availability have been saved.</div>
                <?php endif; ?>

                <div class="mp-coach-dashboard__card mp-coach-dashboard__profile">
                    <div class="mp-coach-dashboard__section-heading">
                        <div>
                            <h2>Profile details</h2>
                            <p>Tell players about your coaching experience and the sports you teach.</p>
                        </div>
                    </div>
                    <?php
                    acf_form([
                        'post_id'      => $coach_post_id,
                        'new_post'     => [
                            'post_type'   => 'coach',
                            'post_status' => 'publish',
                            'post_author' => $current_user->ID,
                            'post_title'  => $display_name,
                        ],
                        'fields'       => [
                            'field_6974b415e4c46', // Nickname
                            'field_6985aadcef579', // Sports
                            'field_6974b662e4c4e', // About me
                            'field_6974b453e4c47', // Gender
                            'field_6974b490e4c48', // Address
                            'field_6974b4a8e4c49', // Phone
                            'field_6974b630e4c4c', // Hourly rate
                            'field_6974b618e4c4b', // Featured image
                            'field_6974b5e7e4c4a', // Gallery
                            'field_6974b643e4c4d', // Availability JSON, edited through calendar below
                        ],
                        'submit_value' => $coach_post ? 'Save coach profile' : 'Create coach profile',
                        'return'       => add_query_arg('updated', 'true', home_url('/dashboard/update/')),
                        'html_form'    => true,
                    ]);
                    ?>
                </div>

                <div class="mp-coach-dashboard__card mp-coach-dashboard__availability">
                    <div class="mp-coach-dashboard__section-heading">
                        <div>
                            <h2>Manage availability</h2>
                            <p>Select a future time range to add availability. Select an available slot to remove it. Bookings are locked.</p>
                        </div>
                    </div>
                    <div class="mp-coach-dashboard__legend" aria-label="Calendar legend">
                        <span><i class="mp-calendar-key mp-calendar-key--available"></i> Available</span>
                        <span><i class="mp-calendar-key mp-calendar-key--pending"></i> Booking pending</span>
                        <span><i class="mp-calendar-key mp-calendar-key--booked"></i> Booked</span>
                    </div>
                    <div id="availability-calendar-frontend" aria-label="Coach availability calendar"></div>
                </div>
            </section>
        </div>
    </div>
</main>

<script>
jQuery(function ($) {
    if (typeof URLSearchParams === 'undefined') return;
    if (new URLSearchParams(window.location.search).get('updated') === 'true' && typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Profile saved',
            text: 'Your coach profile and availability have been updated.',
            confirmButtonText: 'OK'
        }).then(function () {
            window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
        });
    }
});
</script>

<?php get_footer(); ?>
