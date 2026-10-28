<?php
// Template Name: Landing Page

get_header();

$heading_text         = 'Book, Pay, Play';
$subtext              = get_field('subtext') ?: 'Search courts, coaches, and locations near you in seconds.';
$court_heading        = 'Top-rated Courts in Cebu City';
$number_court_display = absint(get_field('number_court_display')) ?: 8;
$learner_heading      = 'Tailored for Tennis Learners';
$learner_copy         = 'We are a team of passionate tennis coaches committed to delivering high-quality, inclusive training programs that empower youth, foster community engagement, and promote long-term athletic development.';
$learner_image        = get_template_directory_uri() . '/img/banner-home.jpg';
?>

<main class="mp-home">
    <section class="mp-home-hero" aria-labelledby="mp-home-title">
        <div class="mp-home-hero-inner">
            <div class="mp-home-wordmark" aria-hidden="true">MATCHPOINT</div>
            <h1 id="mp-home-title"><?php echo esc_html($heading_text); ?></h1>
            <p class="mp-home-subtitle"><?php echo esc_html($subtext); ?></p>

            <div class="mp-home-search" role="search">
                <label class="screen-reader-text" for="coach-search">Search courts, coaches, or locations</label>
                <div class="mp-home-search-location">
                    <span class="mp-home-search-label">Location</span>
                    <select id="coach-search" aria-label="Search courts, coaches, or locations" style="width:100%"></select>
                </div>
                <span class="mp-home-search-divider" aria-hidden="true"></span>
                <label class="mp-home-sport-select-wrap" for="home-sport">
                    <span class="mp-home-search-label">Sport</span>
                    <select id="home-sport" aria-label="Choose a sport">
                        <option value="">Tennis, Padel...</option>
                        <option value="tennis">Tennis</option>
                        <option value="pickleball">Pickleball</option>
                        <option value="padel">Padel</option>
                    </select>
                </label>
                <button id="search-btn" type="button" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span class="screen-reader-text">Search</span>
                </button>
            </div>
        </div>
    </section>

    <section class="mp-home-courts" id="featured-courts" aria-labelledby="mp-courts-title">
        <div class="mp-home-container">
            <nav class="mp-home-categories" aria-label="Explore MatchPoint">
                <a href="<?php echo esc_url(add_query_arg('sport', 'tennis', home_url('/court/'))); ?>"><i class="fa-solid fa-table-tennis-paddle-ball" aria-hidden="true"></i><span>Tennis</span></a>
                <a href="<?php echo esc_url(add_query_arg('sport', 'pickleball', home_url('/court/'))); ?>"><i class="fa-solid fa-table-tennis-paddle-ball" aria-hidden="true"></i><span>Pickleball</span></a>
                <a href="<?php echo esc_url(add_query_arg('sport', 'padel', home_url('/court/'))); ?>"><i class="fa-solid fa-table-tennis-paddle-ball" aria-hidden="true"></i><span>Padel</span></a>
                <a href="<?php echo esc_url(home_url('/coaches/')); ?>"><i class="fa-solid fa-user-group" aria-hidden="true"></i><span>Find Coaches</span></a>
                <a href="<?php echo esc_url(home_url('/court/')); ?>"><i class="fa-solid fa-trophy" aria-hidden="true"></i><span>Tournaments</span></a>
            </nav>

            <div class="mp-home-section-heading">
                <div>
                    <h2 id="mp-courts-title"><?php echo esc_html($court_heading); ?></h2>
                </div>
                <a class="mp-home-view-all" href="<?php echo esc_url(home_url('/court/')); ?>">View all courts <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="mp-home-court-grid">
                <?php
                $courts = new WP_Query([
                    'post_type'      => 'service',
                    'posts_per_page' => $number_court_display,
                    'post_status'    => 'publish',
                    'orderby'        => 'rand',
                ]);

                if ($courts->have_posts()) :
                    while ($courts->have_posts()) :
                        $courts->the_post();
                        $author_id = get_the_author_meta('ID');
                        if (function_exists('hash_is_user_member') && !hash_is_user_member($author_id)) {
                            continue;
                        }

                        $image = get_field('featured_image');
                        $image_url = (is_array($image) && !empty($image['url']))
                            ? $image['url']
                            : get_template_directory_uri() . '/img/placeholder-400x400.jpg';
                        $address = get_field('address');
                        $court_name = get_field('court_name_gym') ?: get_the_title();
                        $hourly_rate = get_field('hourly_rate');
                        $rating_data = function_exists('hash_get_rating_data') ? hash_get_rating_data(get_the_ID()) : ['average' => 0];
                        ?>
                        <article class="mp-home-court-card">
                            <a class="mp-home-court-image" href="<?php the_permalink(); ?>" style="background-image:url('<?php echo esc_url($image_url); ?>')" aria-label="View <?php echo esc_attr($court_name); ?>">
                                <span class="mp-home-favorite" aria-hidden="true"><i class="fa-regular fa-heart"></i></span>
                            </a>
                            <div class="mp-home-court-info">
                                <div class="mp-home-court-title-row">
                                    <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html($court_name); ?></a></h3>
                                    <span class="mp-home-rating"><i class="fa-solid fa-star" aria-hidden="true"></i> <?php echo esc_html(number_format_i18n((float) $rating_data['average'], 1)); ?></span>
                                </div>
                                <?php if ($address) : ?>
                                    <p class="mp-home-court-address"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?php echo esc_html($address); ?></p>
                                <?php endif; ?>
                                <?php if ($hourly_rate) : ?>
                                    <p class="mp-home-court-rate">₱<?php echo esc_html($hourly_rate); ?> <span>/ hour</span></p>
                                <?php endif; ?>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="mp-home-empty">Courts will appear here as they become available.</p>';
                endif;
                ?>
            </div>
        </div>
    </section>

    <section class="mp-home-learners" aria-labelledby="mp-learners-title">
        <div class="mp-home-container mp-home-learners-inner">
            <div class="mp-home-learners-copy">
                <span class="mp-home-eyebrow">Our Mission</span>
                <h2 id="mp-learners-title"><?php echo esc_html($learner_heading); ?></h2>
                <p><?php echo esc_html($learner_copy); ?></p>
                <div class="mp-home-benefits">
                    <div><i class="fa-solid fa-user-check" aria-hidden="true"></i><span><strong>Expert Coaches</strong><small>Learn from experienced professionals.</small></span></div>
                    <div><i class="fa-solid fa-people-group" aria-hidden="true"></i><span><strong>Inclusive Community</strong><small>Programs for players of all ages.</small></span></div>
                </div>
            </div>
            <img class="mp-home-learners-image" src="<?php echo esc_url($learner_image); ?>" alt="Tennis court and equipment" loading="lazy">
        </div>
    </section>
</main>

<?php get_footer(); ?>
