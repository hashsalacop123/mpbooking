<?php 
function allow_custom_roles_media_upload() {
    $roles = ['coach', 'player', 'court']; // add your custom roles here

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role && ! $role->has_cap('upload_files')) {
            $role->add_cap('upload_files');
        }
    }
}
add_action('init', 'allow_custom_roles_media_upload');


function restrict_media_library_to_own_uploads( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Allow admins to see everything
    if ( current_user_can('manage_options') ) {
        return;
    }

    // Only affect media library
    if ( $query->get('post_type') === 'attachment' ) {
        $query->set('author', get_current_user_id());
    }
}
add_action('pre_get_posts', 'restrict_media_library_to_own_uploads');

function restrict_media_library_to_current_user( $query ) {

    // Only affect AJAX requests for media browsing
    if ( defined('DOING_AJAX') && DOING_AJAX ) {

        // Only target the media library browsing request
        if ( isset($_REQUEST['action']) && $_REQUEST['action'] === 'query-attachments' ) {

            $user = wp_get_current_user();
            $restricted_roles = ['coach', 'player', 'court']; // your custom roles

            // Restrict only these roles
            if ( array_intersect( $restricted_roles, (array) $user->roles ) ) {
                $query['author'] = get_current_user_id();
            }

            // Admins can still see everything
            if ( current_user_can('manage_options') ) {
                unset($query['author']);
            } 
        }
    }

    return $query;
}
add_filter('ajax_query_attachments_args', 'restrict_media_library_to_current_user');

// Ensure custom roles can edit their own posts and upload files
add_action('init', function () {

    $roles = ['coach', 'player', 'court'];

    foreach ($roles as $role_name) {
        $role = get_role($role_name);

        if ($role) {
            $role->add_cap('upload_files');
            $role->add_cap('edit_posts');
            $role->add_cap('edit_published_posts');
        }
    }
});



/**
 * Redirect logged-in users to a specific page
 *
 * @param string $redirect_url URL to redirect logged-in users to. Default is '/dashboard'.
 */
function redirect_user_login($redirect_url = '/dashboard') {
    if ( is_user_logged_in() ) {
        wp_redirect( esc_url( $redirect_url ) );
        exit;
    }
}

add_action('update_post_meta', function($meta_id, $post_id, $meta_key, $meta_value){
    // Only target bookings
    $post = get_post($post_id);
    if(!$post || $post->post_type !== 'booking') return;

    // Only target our status field
    if($meta_key !== 'status') return;

    // Only send if changed to approved
    if($meta_value === 'approved'){
        $user_email = get_post_meta($post_id, 'user_email', true);
        $user_name  = get_post_meta($post_id, 'user_name', true);
        $coach_id   = get_post_meta($post_id, 'coach_id', true);
        $start      = get_post_meta($post_id, 'start', true);
        $end        = get_post_meta($post_id, 'end', true);
        $comment    = get_post_meta($post_id, 'comment', true);

        if($user_email){
            $subject = 'Booking Approved';
            $message = "Hi $user_name,\n\nYour booking request for Coach #$coach_id has been approved.\n";
            $message .= "Start: $start\nEnd: $end\nComment: $comment\n\nSee you then!";
            wp_mail($user_email, $subject, $message);
        }
    }

}, 10, 4);

add_action('after_setup_theme', function () {

    // Save ACF JSON inside theme
    add_filter('acf/settings/save_json', function ($path) {
        return get_stylesheet_directory() . '/acf-json';
    });

    // Load ACF JSON from theme
    add_filter('acf/settings/load_json', function ($paths) {
        $paths[] = get_stylesheet_directory() . '/acf-json';
        return $paths;
    });

});

/**
 * AJAX search for coach + service (ACF + author + title)
 */
function search_coaches_ajax() {

    check_ajax_referer('booking_nonce', 'nonce'); // security

    $search = isset($_REQUEST['q']) ? sanitize_text_field($_REQUEST['q']) : '';
    error_log('AJAX REQUEST: ' . print_r($_REQUEST, true));
    if (empty($search)) {
        wp_send_json([]);
    }

    $results = [];

    /**
     * STEP 1: SEARCH USERS (for author matching)
     */
    $user_ids = [];
    $users = get_users([
        'search'         => '*' . esc_attr($search) . '*',
        'search_columns' => ['user_login', 'display_name'],
    ]);

    if (!empty($users)) {
        foreach ($users as $user) {
            $user_ids[] = $user->ID;
        }
    }

    /**
     * STEP 2: GET POSTS (no strict filtering)
     */
    $query = new WP_Query([
        'post_type'      => ['coach', 'service'],
        'posts_per_page' => -1, // get all, filter manually
        'post_status'    => 'publish',
    ]);

    /**
     * STEP 3: LOOP + MATCH
     */
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $post_id   = get_the_ID();
            $title     = get_the_title() ?: '';
            $post_type = get_post_type();
            $author_id = get_the_author_meta('ID');

            // ACF fields (safe fallback)
            $nickname  = get_field('nick_name', $post_id) ?: '';
            $address   = get_field('address', $post_id) ?: '';
            $court     = get_field('court_name_gym', $post_id) ?: '';

            // User fields (safe fallback)
            $first = get_user_meta($author_id, 'first_name', true) ?: '';
            $last  = get_user_meta($author_id, 'last_name', true) ?: '';

            /**
             * MULTI-WORD SEARCH SUPPORT
             */
            $search_terms = explode(' ', strtolower($search));
            $match = false;

            foreach ($search_terms as $term) {

                if (
                    stripos($title, $term) !== false ||
                    stripos($nickname, $term) !== false ||
                    stripos($address, $term) !== false ||
                    stripos($court, $term) !== false ||
                    stripos($first, $term) !== false ||
                    stripos($last, $term) !== false ||
                    in_array($author_id, $user_ids)
                ) {
                    $match = true;
                    break;
                }
            }

            /**
             * ADD RESULT
             */
            if ($match) {

                $label = $title;

                if ($post_type === 'service') {
                    $label .= ' (Court)';
                    if (!empty($address)) {
                        $label .= ' - ' . $address;
                    }
                } else {
                    $label .= ' (Coach)';
                    if (!empty($first) || !empty($last)) {
                        $label .= ' - ' . $first . ' ' . $last;
                    }
                }

                $results[] = [
                    'id'   => get_permalink(), // for redirect
                    'text' => $label,
                ];
            }

            // LIMIT results (important for performance)
            if (count($results) >= 10) {
                break;
            }
        }
    }

    wp_reset_postdata();

    wp_send_json($results);
}

add_action('wp_ajax_search_coaches', 'search_coaches_ajax');
add_action('wp_ajax_nopriv_search_coaches', 'search_coaches_ajax');


add_action('wp_ajax_update_user_status', 'handle_update_user_status');

function handle_update_user_status() {

    // Security check
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'booking_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    // Get values
    $user_id = intval($_POST['user_id']);
    $status  = sanitize_text_field($_POST['status']);

    if (!$user_id) {
        wp_send_json_error('Invalid user ID');
    }

    // Get user
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        wp_send_json_error('User not found');
    }

    // Get OLD status
    $old_status = get_field('registration_status', 'user_' . $user_id);

    // If no change, skip
    if ($old_status === $status) {
        wp_send_json_success([
            'message' => 'No changes made',
            'user_id' => $user_id,
            'status'  => $status
        ]);
    }

    // Update ACF field
    update_field('registration_status', $status, 'user_' . $user_id);

    // Set email to HTML
    add_filter('wp_mail_content_type', function () {
        return 'text/html';
    });

    $subject = '';
    $message = '';

    /* =====================
     * MEMBER (APPROVED)
     * ===================== */
    if ($status === 'member') {

        $subject = 'Your Account Has Been Approved 🎉';
        $message = "
            <h2>Hi {$user->first_name},</h2>
            <p>Your registration has been <strong>approved</strong>.</p>
            <p>You may now log in and start using your account.</p>
            <p><a href='" . wp_login_url('/login') . "'>Login here</a></p>
            <p>— " . get_bloginfo('name') . "</p>
        ";
    }

    /* =====================
     * REJECTED
     * ===================== */
    elseif ($status === 'rejected') {

        $subject = 'Your Registration Status';
        $message = "
            <h2>Hi {$user->first_name},</h2>
            <p>We regret to inform you that your registration was <strong>rejected</strong>.</p>
            <p>If you believe this is a mistake, please contact us.</p>
            <p>— " . get_bloginfo('name') . "</p>
        ";
    }

    /* =====================
     * PENDING
     * ===================== */
    elseif ($status === 'pending') {

        $subject = 'Your Registration is Pending';
        $message = "
            <h2>Hi {$user->first_name},</h2>
            <p>Your registration is currently <strong>pending review</strong>.</p>
            <p>We will notify you once it has been reviewed.</p>
            <p>— " . get_bloginfo('name') . "</p>
        ";
    }

    // Send email if defined
    if (!empty($subject) && !empty($message)) {
        wp_mail($user->user_email, $subject, $message);
    }

    wp_send_json_success([
        'message' => 'Updated successfully',
        'user_id' => $user_id,
        'status'  => $status
    ]);
}
/**
 * Redirect wp-login.php to custom /login/ page
 * (safe: does NOT break logout or admin)
 */
function hash_redirect_wp_login_safe() {

    // Only target wp-login.php
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') === false) {
        return;
    }

    // Allow logout to proceed normally
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        return;
    }

    // Allow admin login (optional but recommended)
    if (is_admin()) {
        return;
    }

    // Build redirect URL
    $redirect = home_url('/login/');

    // Preserve redirect_to if exists
    if (!empty($_GET['redirect_to'])) {
        $redirect = add_query_arg('redirect_to', $_GET['redirect_to'], $redirect);
    }

    wp_redirect($redirect);
    exit;
}
add_action('init', 'hash_redirect_wp_login_safe');
function theme_setup_features() {
    // Enable Featured Image for posts
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'theme_setup_features');
?>