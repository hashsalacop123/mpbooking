<?php
/**
 * Force login before allowing comment submission
 */
function hash_force_login_to_comment($commentdata) {

    // If user is NOT logged in, block submission
    if (!is_user_logged_in()) {
        wp_die('You must be logged in to leave a review.');
    }

    return $commentdata;
}
add_filter('preprocess_comment', 'hash_force_login_to_comment');

/**
 * Enable comments (reviews) for specific CPTs only
 */
function hash_enable_reviews_for_cpts($open, $post_id) {
    $allowed_cpts = ['coach', 'service']; // change this if needed

    if (in_array(get_post_type($post_id), $allowed_cpts)) {
        return true;
    }

    return $open;
}
add_filter('comments_open', 'hash_enable_reviews_for_cpts', 10, 2);


/**
 * Add clickable star rating UI
 */
function hash_review_rating_field() {
    if (!is_user_logged_in()) return;
    ?>

    <p class="comment-form-rating">
        <label>Your Rating</label>
        <div class="star-rating">
            <span class="star" data-value="5">★</span>
            <span class="star" data-value="4">★</span>
            <span class="star" data-value="3">★</span>
            <span class="star" data-value="2">★</span>
            <span class="star" data-value="1">★</span>
        </div>
        <input type="hidden" name="rating" id="rating" required>
    </p>

    <?php
}
add_action('comment_form_logged_in_after', 'hash_review_rating_field');


/**
 * Enqueue star rating script
 */
function hash_review_scripts() {
    wp_add_inline_script('jquery', "
        jQuery(document).ready(function($){
            $('.star').on('click', function(){
                var rating = $(this).data('value');
                $('#rating').val(rating);

                $('.star').removeClass('active');
                $(this).addClass('active');
                $(this).prevAll().addClass('active');
                $(this).nextAll().removeClass('active');
            });
        });
    ");
}
add_action('wp_enqueue_scripts', 'hash_review_scripts');


/**
 * Save rating into comment meta
 */
function hash_save_review_rating($comment_id) {
    if (isset($_POST['rating']) && $_POST['rating'] !== '') {
        add_comment_meta($comment_id, 'rating', intval($_POST['rating']));
    }
}
add_action('comment_post', 'hash_save_review_rating');




/**
 * Get average rating of a post
 */
function hash_get_average_rating($post_id) {
    global $wpdb;

    $avg = $wpdb->get_var($wpdb->prepare("
        SELECT AVG(meta_value)
        FROM {$wpdb->commentmeta}
        WHERE meta_key = 'rating'
        AND comment_id IN (
            SELECT comment_ID FROM {$wpdb->comments}
            WHERE comment_post_ID = %d
            AND comment_approved = 1
        )
    ", $post_id));

    return $avg ? round($avg, 1) : 0;
}


/**
 * Display average rating
 */
function hash_display_average_rating($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();

    $avg = hash_get_average_rating($post_id);

    if ($avg > 0) {
        return '<div class="average-rating"><strong>' . $avg . '</strong> ⭐</div>';
    }

    return '<div class="average-rating">No reviews yet</div>';
}


/**
 * Require login for reviews
 */
function hash_review_login_required($must_log_in) {
    return '<p>You must be logged in to leave a review.</p>';
}
add_filter('comment_form_must_log_in', 'hash_review_login_required');


/**
 * Prevent duplicate reviews per user
 */
/**
 * Prevent duplicate reviews but allow replies
 */
function hash_prevent_duplicate_reviews($commentdata) {

    // Allow replies (threaded comments)
    if (!empty($commentdata['comment_parent'])) {
        return $commentdata;
    }

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $post_id = $commentdata['comment_post_ID'];

        $existing = get_comments([
            'user_id' => $user_id,
            'post_id' => $post_id,
            'parent'  => 0, // only top-level reviews
            'count'   => true
        ]);

        if ($existing > 0) {
            wp_die('You already reviewed this.');
        }
    }

    return $commentdata;
}
add_filter('preprocess_comment', 'hash_prevent_duplicate_reviews');


/**
 * Remove "Logged in as..."
 */
function hash_remove_logged_in_message($args) {
    $args['logged_in_as'] = '';
    return $args;
}
add_filter('comment_form_defaults', 'hash_remove_logged_in_message');


/**
 * Remove "Required fields are marked *"
 */
function hash_remove_required_note() {
    return '';
}
add_filter('comment_form_required_fields', 'hash_remove_required_note');


/**
 * Remove website field
 */
function hash_remove_url_field($fields) {
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields', 'hash_remove_url_field');


/**
 * SHORTCODE: [hash_reviews]
 */
function hash_reviews_shortcode() {
    ob_start();

    echo '<div class="hash-reviews-wrapper">';

    echo hash_display_average_rating();

    echo '<div class="review-list">';
    comments_template();
    echo '</div>';

    echo '</div>';

    return ob_get_clean();
}
add_shortcode('hash_reviews', 'hash_reviews_shortcode');

/**
 * Force all reviews to be pending
 */
function hash_force_reviews_pending($approved, $commentdata) {
    return 0; // always pending
}
add_filter('pre_comment_approved', 'hash_force_reviews_pending', 10, 2);

/**
 * Approve comment (only post author)
 */
function hash_approve_comment() {

    if (!is_user_logged_in()) {
        wp_send_json_error('Not allowed');
    }

    $comment_id = intval($_POST['comment_id']);
    $comment    = get_comment($comment_id);

    if (!$comment) {
        wp_send_json_error('Invalid comment');
    }

    $post = get_post($comment->comment_post_ID);

    // Check if current user is the post author
    if ($post->post_author != get_current_user_id()) {
        wp_send_json_error('Not allowed');
    }

    wp_set_comment_status($comment_id, 'approve');

    wp_send_json_success('Approved');
}
add_action('wp_ajax_hash_approve_comment', 'hash_approve_comment');
function hash_review_approve_script() {
    wp_add_inline_script('jquery', "
        jQuery(document).on('click', '.approve-review', function(){
            var btn = jQuery(this);
            var comment_id = btn.data('id');

            jQuery.post('/wp-admin/admin-ajax.php', {
                action: 'hash_approve_comment',
                comment_id: comment_id
            }, function(response){
                if(response.success){
                    btn.text('Approved').prop('disabled', true);
                } else {
                    alert(response.data);
                }
            });
        });
    ");
}
add_action('wp_enqueue_scripts', 'hash_review_approve_script');

/**
 * Get rating data (average + count)
 */
function hash_get_rating_data($post_id) {
    global $wpdb;

    $results = $wpdb->get_row($wpdb->prepare("
        SELECT 
            AVG(meta_value) as avg_rating,
            COUNT(meta_value) as total_reviews
        FROM {$wpdb->commentmeta}
        WHERE meta_key = 'rating'
        AND comment_id IN (
            SELECT comment_ID FROM {$wpdb->comments}
            WHERE comment_post_ID = %d
            AND comment_approved = 1
        )
    ", $post_id));

    return [
        'average' => $results->avg_rating ? round($results->avg_rating, 1) : 0,
        'count'   => $results->total_reviews ? intval($results->total_reviews) : 0,
    ];
}

/**
 * Display star rating with fill + count
 */
/**
 * Display rating summary (like your screenshot)
 */
function hash_display_rating_summary($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();

    $data = hash_get_rating_data($post_id);
    $avg  = $data['average'];
    $count = $data['count'];

    $percentage = ($avg / 5) * 100;

    ob_start();
    ?>

    <div class="rating-box">

        <div class="rating-left">
            <div class="review-count"><?php echo $count; ?></div>
            <div class="review-label">Reviews</div>
        </div>

        <div class="rating-right">
            <div class="stars-outer">
                <div class="stars-inner" style="width: <?php echo $percentage; ?>%;"></div>
            </div>
            <div class="rating-number"><?php echo $avg; ?></div>
        </div>

    </div>

    <?php
    return ob_get_clean();
}