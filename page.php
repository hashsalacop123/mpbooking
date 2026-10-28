<?php
/**
 * Template Name: Default Page
 */

get_header();
$privacy_policy_id = (int) get_option('wp_page_for_privacy_policy');
$is_privacy_policy = ($privacy_policy_id && is_page($privacy_policy_id)) || is_page('privacy-policy');
?>
<main id="primary" class="site-main mp-standard-page <?php echo $is_privacy_policy ? 'mp-privacy-page' : ''; ?>">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ?>
            <?php if ($is_privacy_policy) : ?>
                <section class="mp-policy-hero">
                    <div class="mp-policy-container">
                        <span class="mp-auth-eyebrow">MatchPoint policies</span>
                        <h1><?php the_title(); ?></h1>
                        <p>How MatchPoint handles information when you use our booking services.</p>
                    </div>
                </section>
            <?php endif; ?>

            <div class="<?php echo $is_privacy_policy ? 'mp-policy-container' : 'container'; ?> mp-standard-page-content">
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php if (!$is_privacy_policy) : ?>
                        <header class="entry-header"><h1 class="entry-title"><?php the_title(); ?></h1></header>
                    <?php endif; ?>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            </div>

            <?php
        endwhile;
    endif;
    ?>
</main>


<?php
get_footer();
