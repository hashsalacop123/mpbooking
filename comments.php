<div id = "review">
<hr>
<h3>Reviews</h3>
<?php if (have_comments()) : ?>
    <ul class="comment-list">
        <?php
        wp_list_comments([
            'style' => 'ul',
            'callback' => 'hash_custom_comment'
        ]);
        ?>
    </ul>
<?php endif; ?>

<?php comment_form(); ?>

<?php
function hash_custom_comment($comment, $args, $depth) {

    $current_user_id = get_current_user_id();
    $post = get_post();
    $rating = get_comment_meta($comment->comment_ID, 'rating', true);
?>

<li id="comment-<?php comment_ID(); ?>">

    <strong><?php comment_author(); ?></strong>

    <?php if ($rating): ?>
        <div><?php echo str_repeat('⭐', $rating); ?></div>
    <?php endif; ?>

    <div><?php comment_text(); ?></div>

    <?php if ($comment->comment_approved == '0'): ?>
        <p style="color: orange;">⏳ Pending</p>
    <?php endif; ?>

    <?php if (
        $post &&
        $current_user_id == $post->post_author &&
        $comment->comment_approved == '0'
    ): ?>
        <button class="approve-review" data-id="<?php echo $comment->comment_ID; ?>">
            Approve
        </button>
    <?php endif; ?>

</li>

<?php } ?>
</div>