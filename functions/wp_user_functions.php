<?php 



// ==========================
// FRONT-END USER REGISTRATION
// ==========================
add_action('acf/save_post', 'acf_user_registration_handler', 20);
function acf_user_registration_handler($post_id) {
    if ($post_id !== 'new_user') return;
    if (!isset($_POST['acf'])) return;

    $acf = $_POST['acf'];

    // Actual ACF field keys
    $email      = sanitize_email($acf['field_6915cb87b7a42']);
    $password   = sanitize_text_field($acf['field_6915cb98b7a43']);
    $first_name = sanitize_text_field($acf['field_6915c59b88f11']);
    $last_name  = sanitize_text_field($acf['field_6915cb7bb7a41']);
    $valid_id   = $acf['field_6915cbdab7a45']; // Image field ID


    // Create user
    $userdata = [
        'user_login' => $email,
        'user_email' => $email,
        'user_pass'  => $password,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'valid_id' => $valid_id,
        'role'       => 'editor', // Change role if needed
    ];

    $user_id = wp_insert_user($userdata);

    if (is_wp_error($user_id)) {
        error_log('User creation error: ' . $user_id->get_error_message());
        return;
    }

    // Save extra fields


 
    // Auto-login and redirect
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    wp_safe_redirect(home_url('/dashboard/'));

    exit;
}

function my_acf_enqueue_uploader() {
    if (function_exists('acf_form_head')) {
        wp_enqueue_media();
    }
}
add_action('wp_enqueue_scripts', 'my_acf_enqueue_uploader');

add_filter('acf/load_value', function($value, $post_id, $field) {
    if ($post_id === 'new_user') {
        return ''; // force blank field
    }
    return $value;
}, 10, 3);

add_filter('acf/load_value/type=image', function($value, $post_id, $field) {
    if ($post_id === 'new_user') {
        return null; 
    }
    return $value;
}, 10, 3);

// ======login=====
// Custom login form shortcode
// Handle login before output
add_action('template_redirect', function() {

    // Only run on login page
    if ( is_page('login') && isset($_POST['custom_login_submit']) ) {

        // Check nonce
        if ( isset($_POST['custom-login-nonce']) && wp_verify_nonce($_POST['custom-login-nonce'], 'custom-login-action') ) {

            $username = sanitize_text_field($_POST['username']);
            $password = sanitize_text_field($_POST['password']);
            $remember = isset($_POST['rememberme']);

            // Convert email to username if needed
            if ( is_email($username) ) {
                $user_obj = get_user_by('email', $username);
                if ($user_obj) $username = $user_obj->user_login;
            }

            $creds = array(
                'user_login' => $username,
                'user_password' => $password,
                'remember' => $remember
            );

            $user = wp_signon($creds, is_ssl());

            if ( !is_wp_error($user) ) {
                // Successful login, redirect
                $redirect_to = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : home_url('/dashboard/');
                wp_safe_redirect($redirect_to);
                exit;
            } else {
                // Save errors in a session or transient to show in the shortcode
                set_transient('login_errors', $user->get_error_message(), 30);
            }
        }
    }

    // Redirect logged-in users away from login page
    if ( is_page('login') && is_user_logged_in() ) {
        wp_safe_redirect( home_url('/dashboard/') );
        exit;
    }
});

function custom_user_login_shortcode() {



    ob_start(); ?>
  

    <form method="post" class="custom-login-form">
        <p>
            <label for="username">Username or Email</label><br>
            <input type="text" name="username" id="username" required>
        </p>
        <p>
            <label for="password">Password</label><br>
            <input type="password" name="password" id="password" required>
        </p>
        <p>
            <input type="checkbox" name="rememberme" id="rememberme" value="1">
            <label for="rememberme">Remember Me</label>
        </p>
        <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/dashboard/')); ?>">
        <?php wp_nonce_field('custom-login-action','custom-login-nonce'); ?>
        <p>
            <input type="submit" name="custom_login_submit" value="Login">
        </p>
        <p>
            <a href="<?php echo wp_lostpassword_url(); ?>">Forgot Password?</a>
        </p>
 
    </form>
    <?php

$login_error = get_transient('login_errors');
delete_transient('login_errors'); // clear after getting


 if (!empty($login_error)) : ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: <?php echo json_encode($login_error); ?>,
                confirmButtonText: 'OK'
            });
        });
    </script>
<?php endif;
    return ob_get_clean();
}
add_shortcode('user_login', 'custom_user_login_shortcode');


add_filter('acf/load_field', function ($field) {
    if ($field['key'] === 'field_6915c59b88f11') {  // replace with your field key
        $field['placeholder'] = 'First Name';
    }

     if ($field['key'] === 'field_6915cb7bb7a41') {  // replace with your field key
        $field['placeholder'] = 'Last Name';
    }

     if ($field['key'] === 'field_6915cb87b7a42') {  // replace with your field key
        $field['placeholder'] = 'Email';
    }

     if ($field['key'] === 'field_6915cb98b7a43') {  // replace with your field key
        $field['placeholder'] = 'Password';
    }


 

 
    return $field;
});

function add_custom_user_roles() {

    add_role(
        'coach',
        'Coach',
        [
            'read' => true,
        ]
    );

    add_role(
        'player',
        'Player',
        [
            'read' => true,
        ]
    );

    add_role(
        'court',
        'Court',
        [
            'read' => true,
        ]
    );

}
add_action('init', 'add_custom_user_roles');

add_filter('acf/update_value/name=registration_status', 'acf_user_registration_status_email', 10, 3);

function acf_user_registration_status_email($value, $post_id, $field) {

    if (strpos($post_id, 'user_') !== 0) {
        return $value;
    }

    // ❗ Prevent running on AJAX (important)
    if ( defined('DOING_AJAX') && DOING_AJAX ) {
        return $value;
    }

    $user_id = str_replace('user_', '', $post_id);
    $user    = get_user_by('ID', $user_id);

    if (!$user) {
        return $value;
    }

    $old_status = get_user_meta($user_id, 'registration_status', true);
    $new_status = $value;

    if ($old_status === $new_status) {
        return $value;
    }

    add_filter('wp_mail_content_type', function () {
        return 'text/html';
    });

    if ($new_status === 'member') {

        // ❌ REMOVED ROLE CHANGE

        wp_mail(
            $user->user_email,
            'Your Account Has Been Approved 🎉',
            "
            <h2>Hi {$user->first_name},</h2>
            <p>Your registration has been <strong>approved</strong>.</p>
            <p>You may now log in.</p>
            <p><a href='" . wp_login_url('/login') . "'>Login here</a></p>
            <p>— " . get_bloginfo('name') . "</p>
            "
        );
    }

    if ($new_status === 'rejected') {

        wp_mail(
            $user->user_email,
            'Your Registration Status',
            "
            <h2>Hi {$user->first_name},</h2>
            <p>We regret to inform you that your registration was <strong>rejected</strong>.</p>
            <p>If you believe this is a mistake, please contact us.</p>
            <p>— " . get_bloginfo('name') . "</p>
            "
        );
    }

    return $value;
}