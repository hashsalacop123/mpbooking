<?php
// Template Name: admin dashboard
acf_form_head(); // Must be first
get_header();
?>

<div class="dasboard-wrapper-page">
    <div class="container">
<div class = "row">
    <!-- SIDEBAR START HERE -->
    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>    </div>
    <!-- CONTTENT START HERE -->
    <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-12">

 <?php
// Get users with specific roles
$args = array(
    'role__in' => array('coach', 'subscriber', 'court'),
    'orderby'  => 'registered',
    'order'    => 'DESC',
);

$user_query = new WP_User_Query($args);
$users = $user_query->get_results();
?>

<table id="users-table" class="display tables-general">
    <thead>
        <tr>
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date Registered</th>
            <th>Role</th>
            <th>Valid ID</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)) : ?>
            <?php foreach ($users as $user) : 

                // Get user meta
                $first_name = get_user_meta($user->ID, 'first_name', true);
                $last_name  = get_user_meta($user->ID, 'last_name', true);

                // Date registered
                $registered = date('F j, Y', strtotime($user->user_registered));
                // Role (get first role)
                $role = !empty($user->roles) ? $user->roles[0] : '';

                // ACF fields (user context)
                $registration_status = get_field('registration_status', 'user_' . $user->ID);

                // ACF image (valid_id)
                $valid_id = get_field('valid_id', 'user_' . $user->ID);

        $valid_id_url = '';

if (is_array($valid_id) && isset($valid_id['url'])) {
    // Image array
    $valid_id_url = $valid_id['url'];

} elseif (is_string($valid_id)) {
    // URL
    $valid_id_url = $valid_id;

} elseif (is_numeric($valid_id)) {
    // Image ID (your case)
    $valid_id_url = wp_get_attachment_url($valid_id);
}
                                $status_color = '';

                                if ($registration_status == 'member') {
                                    $status_color = 'approved-color';
                                } elseif ($registration_status == 'pending') {
                                    $status_color = 'pending-color';
                                } elseif ($registration_status == 'rejected') {
                                    $status_color = 'reject-color';
                                }else {
                                    
                                }
            ?>
                <tr>
                    <td><?php echo esc_html($user->ID); ?></td>
                    <td><?php echo esc_html($first_name); ?></td>
                    <td><?php echo esc_html($last_name); ?></td>
                    <td><?php echo esc_html($registered); ?></td>
                    <td><?php echo esc_html($role); ?></td>
                    <td>
                        <?php if ($valid_id_url) : ?>
                            <a href="<?php echo esc_url($valid_id_url); ?>" target="_blank">View ID</a>
                        <?php else : ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="<?php echo $status_color; ?>">
                             <?php echo esc_html($registration_status); ?>
                        </span>
                    </td>
                   <td class="button-action">
                    <button 
                        class="btn btn-primary open-user-modal"
                        data-id="<?php echo $user->ID; ?>"
                        data-first="<?php echo esc_attr($first_name); ?>"
                        data-last="<?php echo esc_attr($last_name); ?>"
                        data-status="<?php echo esc_attr($registration_status); ?>"
                    >
                        Action
                    </button>
                </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>




    </div>
</div>

    </div>
</div>
<div id="userModal" class="booking-modal" style="display:none;">
    <div class="booking-modal-content">
        <h4>User Details</h4>

        <input type="hidden" id="modal-user-id">

        <label>First Name</label>
        <input type="text" id="modal-user-first" disabled>

        <label>Last Name</label>
        <input type="text" id="modal-user-last" disabled>

        <label>Status</label>
        <select id="modal-user-status">
            <option value="pending">Pending</option>
            <option value="member">Member</option>
            <option value="rejected">Rejected</option>
        </select>

        <div style="margin-top:15px;">
            <button id="update-user-membership" class="btn btn-success">Update</button>
            <button id="close-user-modal" class="btn btn-secondary">Close</button>
        </div>
    </div>
</div>
<?php get_footer(); ?>