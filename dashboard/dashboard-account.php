<?php
// Template Name: My Account
acf_form_head(); // Must be first
get_header();
?>

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
/* Template Name: My Account */

if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url() );
    exit;
}

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$success      = false;

/**
 * FORM SUBMIT
 */
if (
    isset($_POST['update_account']) &&
    wp_verify_nonce($_POST['account_nonce'], 'update_account_nonce')
) {

    // Update basic user data
    wp_update_user([
        'ID'         => $user_id,
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name'  => sanitize_text_field($_POST['last_name']),
        'user_url'   => esc_url_raw($_POST['website']),
    ]);

    // Update password (optional)
    if ( ! empty($_POST['password']) ) {
        wp_set_password($_POST['password'], $user_id);
    }

    // Handle Valid ID image upload (ACF Image Field)
    if ( ! empty($_FILES['valid_id']['name']) ) {

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_handle_upload('valid_id', 0);

        if ( ! is_wp_error($attachment_id) ) {
            // Save image ID to ACF user field
            update_field('valid_id', $attachment_id, 'user_' . $user_id);
        }
    }

    $success = true;
}

/**
 * GET CURRENT VALID ID IMAGE (Image ID)
 */
$valid_id_image_id  = get_user_meta($user_id, 'valid_id', true);
$valid_id_image_url = $valid_id_image_id
    ? wp_get_attachment_image_url((int) $valid_id_image_id, 'medium')
    : '';
?>
<?php hash_show_pending_registration_notice(); ?>

<div class="container ">
    <div class="row justify-content-center">
        
        <div class="col-lg-12">
            <div class=" shadow-sm">
                <div class="card-body">
                    <h4 class="mb-4">My Account</h4>

                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('update_account_nonce', 'account_nonce'); ?>

                        <div class="row">

                            <!-- Email (Disabled) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email"
                                       class="form-control"
                                       value="<?php echo esc_attr($current_user->user_email); ?>"
                                       disabled>
                            </div>

                            <!-- Website -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website</label>
                                <input type="url"
                                       name="website"
                                       class="form-control"
                                       value="<?php echo esc_attr($current_user->user_url); ?>">
                            </div>

                            <!-- First Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text"
                                       name="first_name"
                                       class="form-control"
                                       value="<?php echo esc_attr($current_user->first_name); ?>"
                                       required>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text"
                                       name="last_name"
                                       class="form-control"
                                       value="<?php echo esc_attr($current_user->last_name); ?>"
                                       required>
                            </div>

                            <!-- Valid ID Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid ID</label>

                                <?php if ( $valid_id_image_url ) : ?>
                                    <div class="mb-2">
                                        <img src="<?php echo esc_url($valid_id_image_url); ?>"
                                             class="img-thumbnail"
                                             style="max-height:150px;">
                                    </div>
                                <?php endif; ?>

                                <input type="file"
                                       name="valid_id"
                                       class="form-control"
                                       accept="image/*">
                            </div>

                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password"
                                       name="password"
                                       class="form-control"
                                       placeholder="Leave blank to keep current password">
                            </div>

                        </div>

                        <div class="text-end mt-3">
                            <button type="submit"
                                    name="update_account"
                                    class="btn btn-primary px-4">
                                Update Account
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php if ( $success ) : ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: 'Your account details have been updated successfully.'
});
</script>
<?php endif; ?>    </div>
</div>

    </div>
</div>

<?php get_footer(); ?>