<?php
$query = new WP_Query($args);
$shown_coaches = 0;

if ($query->have_posts()) :
    while ($query->have_posts()) :
        $query->the_post();

        $author_id = get_the_author_meta('ID');
        if (!hash_is_user_member($author_id)) {
            continue;
        }

        $shown_coaches++;
        $nickname = get_field('nick_name') ?: get_the_title();
        $featured_image = get_field('featured_image');
        $hourly = get_field('hourly_rate');
        $about_me = get_field('about_me');
        $short_text = $about_me ? wp_trim_words(wp_strip_all_tags($about_me), 24, '…') : '';
        $default_image = get_template_directory_uri() . '/img/placeholder-400x400.jpg';
        $image_url = (is_array($featured_image) && !empty($featured_image['sizes']['large']))
            ? $featured_image['sizes']['large']
            : ((is_array($featured_image) && !empty($featured_image['url'])) ? $featured_image['url'] : $default_image);
        $image_alt = (is_array($featured_image) && !empty($featured_image['alt']))
            ? $featured_image['alt']
            : $nickname;
        ?>
        <article class="mp-coach-directory-item">
            <a class="mp-coach-directory-card" href="<?php the_permalink(); ?>" aria-label="View <?php echo esc_attr($nickname); ?>'s coach profile">
                <div class="mp-coach-directory-image">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy">
                </div>
                <div class="mp-coach-directory-card-body">
                    <div class="mp-coach-directory-title-row">
                        <h3><?php echo esc_html($nickname); ?></h3>
                        <?php if ($hourly !== '' && $hourly !== null) : ?>
                            <span class="mp-coach-directory-rate">₱<?php echo esc_html(number_format_i18n((float) $hourly)); ?><small>/ hour</small></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($short_text) : ?>
                        <p class="mp-coach-directory-bio"><?php echo esc_html($short_text); ?></p>
                    <?php else : ?>
                        <p class="mp-coach-directory-bio mp-coach-directory-bio-empty">View this coach’s profile for more information.</p>
                    <?php endif; ?>
                    <div class="mp-coach-directory-card-footer">
                        <div class="mp-coach-directory-rating" aria-label="Coach rating">
                            <?php echo hash_display_rating_summary(); ?>
                        </div>
                        <span class="mp-coach-directory-link">View profile <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </div>
            </a>
        </article>
        <?php
    endwhile;
    wp_reset_postdata();

    if (!$shown_coaches) : ?>
        <div class="mp-coaches-empty-state">
            <span class="mp-coaches-empty-icon"><i class="fa-solid fa-user-tie" aria-hidden="true"></i></span>
            <h3>Coach profiles are on the way</h3>
            <p>There are no coach profiles to show right now. Please check back soon.</p>
        </div>
    <?php endif;
else : ?>
    <div class="mp-coaches-empty-state">
        <span class="mp-coaches-empty-icon"><i class="fa-solid fa-user-tie" aria-hidden="true"></i></span>
        <h3>Coach profiles are on the way</h3>
        <p>There are no coach profiles to show right now. Please check back soon.</p>
    </div>
<?php endif; ?>
