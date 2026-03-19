<?php
// Template Name: Registration Page
acf_form_head();

get_header();

wp_enqueue_media();

redirect_user_login(); 
?>

<div class="landing-page-wrapper registration-page-data" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/img/banner-home.jpg' ); ?>');"> 




  
  
        <?php $current_page = get_queried_object();

if ( isset($current_page->post_name) ) {
    if ( $current_page->post_name === 'login' ) { ?>
        <div class = "registration-page-wrapper">
            <div class = "container">
                <div class = "row">
                    <div class = "col-sm-12 col-md-4 offset-md-4">
                        <div class = "registration-form-box ">
                            <h2>Login</h2>
                        <div class="registration-form login-form-box">

                       <?php echo do_shortcode('[user_login]'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } else { ?>
        <div class = "registration-page-wrapper">
        <div class = "container">
            <div class = "row">
                <div class = "col-sm-12 col-md-6 offset-md-3">
                    <div class = "registration-form-box">
<a href="https://tingbook.local/wp-login.php?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="600" data-popupheight="679">
	<img src="Image url" alt="" />
</a>                        <h2>Register</h2>
                    <div class="registration-form">
    <?php
if ( is_user_logged_in() ) {
    wp_redirect( home_url('/dashboard') );
    exit;
}

$success = false;
$error   = '';

 if ( isset($_POST['register_user']) ) {

    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name  = sanitize_text_field($_POST['last_name']);
    $email      = sanitize_email($_POST['email']);
    $password   = $_POST['password'];
    $role       = isset($_POST['user_role']) ? sanitize_text_field($_POST['user_role']) : '';

    $allowed_roles = ['coach', 'player', 'court'];

    if ( empty($email) || empty($password) ) {
        $error = 'Email and password are required.';
    } elseif ( email_exists($email) ) {
        $error = 'Email already exists.';
    } elseif ( empty($role) || ! in_array($role, $allowed_roles, true) ) {
        $error = 'Please select a valid role.';
    } elseif ( empty($_FILES['valid_id']['name']) ) {
        $error = 'Valid ID is required.';
    } else {

        $user_id = wp_create_user($email, $password, $email);

        if ( ! is_wp_error($user_id) ) {

            wp_update_user([
                'ID'         => $user_id,
                'first_name' => $first_name,
                'last_name'  => $last_name,
            ]);

            // Assign role
            $user = new WP_User($user_id);
            $user->set_role($role);
            // ===============================
                // EMAIL NOTIFICATIONS
                // ===============================

                // Admin email
                $admin_email = get_option('admin_email');

                $admin_subject = 'New User Registration';
                $admin_message = "
                A new user has registered on your website.

                Name: {$first_name} {$last_name}
                Email: {$email}
                Role: {$role}
                

                Please review their Valid ID in the admin panel.
                ";

                $admin_headers = ['Content-Type: text/plain; charset=UTF-8'];

                wp_mail($admin_email, $admin_subject, $admin_message, $admin_headers);


                // Registrar (User) email
                $user_subject = 'Welcome! Your Registration Was Successful';
                $user_message = "
                Hi {$first_name},

                Thank you for registering on our website.

                Here are your details: (Please Note this credential)
                Email: {$email}
                Password: {$password}
                Role: {$role}

                Your account is currently under review.
                You will be notified once your account is approved.

                Best regards,
                " . get_bloginfo('name');

                $user_headers = ['Content-Type: text/plain; charset=UTF-8'];

                wp_mail($email, $user_subject, $user_message, $user_headers);

            update_field('registration_status', 'pending', 'user_' . $user_id);

            // Upload Valid ID
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $attachment_id = media_handle_upload('valid_id', 0);

            if ( ! is_wp_error($attachment_id) ) {
                update_field('valid_id', $attachment_id, 'user_' . $user_id);
            }

            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            $success = true;

        } else {
            $error = $user_id->get_error_message();
        }
    }
}

    ?>


                <form method="post" enctype="multipart/form-data">
        <div class="row">

            <!-- First Name -->
            <div class="col-md-6 mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>

            <!-- Last Name -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>

            <!-- Email Address -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <!-- Password -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Valid ID -->
           <!-- Role + Valid ID -->
<div class="col-md-6 mb-3">
    <label class="form-label">Register As</label>
    <select name="user_role" class="form-control" required>
        <option value="">Select Role</option>
        <option value="coach">Coach</option>
        <option value="player">Player</option>
        <option value="court">Court</option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Valid ID</label>
    <input type="file" name="valid_id" class="form-control" accept="image/*" required>
</div>

            <!-- Submit Button -->
            <div class="col-12 mt-3 flex-class">
                <button type="submit" name="register_user" class="btn w-30">
                    Register
                </button>
                <a href = "/login">Login</a>
            </div>

        </div>
    </form>


<!-- SweetAlert -->
<?php if ( $success ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Registration Successful!',
        text: 'Your account has been created successfully.',
        confirmButtonText: 'Go to Dashboard'
    }).then(() => {
        window.location.href = '/dashboard';
    });
});
</script>
<?php endif; ?>

<?php if ( $error ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo esc_js($error); ?>'
    });
});
</script>
<?php endif; ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
   <?php }
}
        
        ?>


</div>
 






<?php get_footer(); ?>