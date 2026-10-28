<?php
get_header();

$coach_id       = get_the_ID();
$coach_author_id = (int) get_post_field('post_author', $coach_id);
$nickname       = get_field('nick_name') ?: get_the_title($coach_id);
$sports         = get_field('sports');
$featured_image = get_field('featured_image');
$image_url      = get_template_directory_uri() . '/img/placeholder-400x400.jpg';
$image_alt      = $nickname;
$hourly_rate    = get_field('hourly_rate');
$phone          = get_field('phone');
$email          = get_the_author_meta('user_email', $coach_author_id);
$about          = get_field('about_me');
$gallery        = get_field('mygallery');
$awards         = get_field('awards');

if (is_array($featured_image) && !empty($featured_image['url'])) {
    $image_url = $featured_image['sizes']['medium'] ?? $featured_image['url'];
    $image_alt = $featured_image['alt'] ?: $nickname;
}

$sport_names = [];
if ($sports instanceof WP_Term) {
    $sport_names[] = $sports->name;
} elseif (is_array($sports)) {
    foreach ($sports as $sport) {
        if ($sport instanceof WP_Term) {
            $sport_names[] = $sport->name;
        } elseif (is_array($sport) && !empty($sport['name'])) {
            $sport_names[] = $sport['name'];
        }
    }
}
?>
<main class="mp-coach-page" data-coach="<?php echo esc_attr($coach_author_id); ?>">
    <div class="mp-coach-container">
        <?php hash_show_pending_registration_notice(); ?>

        <section class="mp-coach-profile" aria-label="Coach profile">
            <img class="mp-coach-avatar" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
            <div class="mp-coach-profile-copy">
                <h1><?php echo esc_html($nickname); ?></h1>
                <?php if ($sport_names) : ?>
                    <p class="mp-coach-subtitle"><?php echo esc_html(implode(' · ', $sport_names)); ?> Coach</p>
                <?php else : ?>
                    <p class="mp-coach-subtitle">Tennis Coach</p>
                <?php endif; ?>
            </div>
            <a class="mp-coach-book-button" href="#booknow">Book Coach</a>
        </section>

        <section class="mp-coach-booking-layout" aria-label="Availability and contact details">
            <div class="mp-coach-calendar-card">
                <?php require get_template_directory() . '/inc/coach-booking-calendar.php'; ?>
            </div>

            <aside class="mp-coach-contact-card">
                <div class="mp-coach-rate-block">
                    <h2>Hourly Range Rate</h2>
                    <p><?php echo $hourly_rate !== '' && $hourly_rate !== null ? esc_html($hourly_rate) : 'Contact for rate'; ?><?php if ($hourly_rate !== '' && $hourly_rate !== null) : ?><span> / hr</span><?php endif; ?></p>
                </div>
                <div class="mp-coach-contact-block">
                    <h2>Contact Details</h2>
                    <?php if ($email) : ?><p><i class="fa-regular fa-envelope" aria-hidden="true"></i><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p><?php endif; ?>
                    <?php if ($phone) : ?><p><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></p><?php endif; ?>
                    <?php if ($address = get_field('address')) : ?><p><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><?php echo esc_html($address); ?></span></p><?php endif; ?>
                </div>
                <div class="mp-coach-social-block">
                    <h2>Social Media</h2>
                    <?php
                    $socials = [
                        'facebook'  => 'fa-facebook-f',
                        'instagram' => 'fa-instagram',
                        'youtube'   => 'fa-youtube',
                        'tiktok'    => 'fa-tiktok',
                    ];
                    $has_socials = false;
                    foreach ($socials as $field => $icon) :
                        $url = get_field($field, 'option');
                        if (!$url) continue;
                        $has_socials = true;
                        ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($field)); ?>"><i class="fa-brands <?php echo esc_attr($icon); ?>" aria-hidden="true"></i></a>
                    <?php endforeach; ?>
                    <?php if (!$has_socials) : ?><span class="mp-coach-no-socials">No social links listed</span><?php endif; ?>
                </div>
            </aside>
        </section>

        <div class="mp-coach-divider"></div>

        <section class="mp-coach-details" aria-label="More about the coach">
            <details class="mp-coach-accordion">
                <summary>About <?php echo esc_html($nickname); ?><span aria-hidden="true"></span></summary>
                <div class="mp-coach-accordion-content">
                    <?php if ($about) : ?>
                        <div class="mp-coach-about-copy"><?php echo wp_kses_post($about); ?></div>
                    <?php else : ?>
                        <p>This coach hasn’t added a profile yet.</p>
                    <?php endif; ?>
                    <?php if ($gallery) : ?>
                        <div class="mp-coach-gallery">
                            <h3>Beyond the court</h3>
                            <div class="mp-coach-gallery-grid">
                                <?php foreach ($gallery as $image) :
                                    if (empty($image['url'])) continue;
                                    $thumb = $image['sizes']['medium'] ?? $image['url'];
                                    ?>
                                    <a href="<?php echo esc_url($image['url']); ?>" data-fancybox="coach-gallery" data-caption="<?php echo esc_attr($image['caption'] ?? ''); ?>">
                                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" loading="lazy">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </details>

            <?php if ($awards) : ?>
                <details class="mp-coach-accordion">
                    <summary>Awards<span aria-hidden="true"></span></summary>
                    <div class="mp-coach-accordion-content"><?php echo wp_kses_post($awards); ?></div>
                </details>
            <?php endif; ?>

            <details class="mp-coach-accordion" id="review">
                <summary>Reviews<span aria-hidden="true"></span></summary>
                <div class="mp-coach-accordion-content mp-coach-review-content">
                    <?php echo hash_display_rating_summary(); ?>
                    <?php comments_template(); ?>
                </div>
            </details>
        </section>
    </div>
</main>

<div class="sharethis-share-buttons" data-type="sticky-share-buttons">
    <span data-network="facebook"></span>
    <span data-network="twitter"></span>
    <span data-network="linkedin"></span>
    <span data-network="email"></span>
    <span data-network="sharethis"></span>
</div>
<?php get_footer(); ?>
