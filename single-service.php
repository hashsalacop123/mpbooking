<?php
get_header();
$service_id = get_the_ID();
$court_name = get_field('court_name_gym', $service_id) ?: get_the_title($service_id);
$court_rows = get_field('court_name', $service_id);
$court_count = is_array($court_rows) ? count($court_rows) : 0;
$genres = get_the_terms($service_id, 'genre');
$genre_names = ($genres && !is_wp_error($genres)) ? wp_list_pluck($genres, 'name') : [];
$images = get_field('images', $service_id);
?>
<main class="mp-court-page">
    <div class="mp-court-container">
        <?php hash_show_pending_registration_notice(); ?>

        <header class="mp-court-profile-header">
            <div>
                <h1><?php echo esc_html($court_name); ?></h1>
                <p><?php echo $court_count ? esc_html($court_count . ' ' . (count($genre_names) ? implode(' · ', $genre_names) : 'Court') . ($court_count === 1 ? '' : 's')) : esc_html(implode(' · ', $genre_names)); ?></p>
            </div>
            <a class="mp-court-book-button" href="#booknow">Book A Court</a>
        </header>

        <?php require get_template_directory() . '/inc/service-booking-calendar.php'; ?>

        <?php if ($images) : ?>
            <section class="mp-court-gallery-section" aria-label="Court photos">
                <h2>Gallery</h2>
                <div class="mp-court-gallery">
                    <?php foreach ($images as $image) :
                        if (empty($image['url'])) continue;
                        $image_large = $image['sizes']['large'] ?? $image['url'];
                        $image_medium = $image['sizes']['medium'] ?? $image_large;
                        ?>
                        <a href="<?php echo esc_url($image['url']); ?>" data-fancybox="court-gallery" data-caption="<?php echo esc_attr($image['caption'] ?? ''); ?>">
                            <img src="<?php echo esc_url($image_medium); ?>" alt="<?php echo esc_attr($image['alt'] ?? $court_name); ?>" loading="lazy">
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="mp-court-information">
            <?php if ($about = get_field('about_me', $service_id)) : ?>
                <article class="mp-court-info-block">
                    <h2>About this court</h2>
                    <div><?php echo wp_kses_post($about); ?></div>
                </article>
            <?php endif; ?>

            <?php
            $amenities = get_field('amenities', $service_id);
            $amenity_labels = [
                'shower' => ['fa-shower', 'Shower'],
                'lockerrooms' => ['fa-lock', 'Locker rooms'],
                'parkingarea' => ['fa-square-parking', 'Parking area'],
                'restrooms' => ['fa-restroom', 'Restrooms'],
                'waitingarea' => ['fa-chair', 'Waiting area'],
                'drinkingwaterorvending' => ['fa-faucet', 'Drinking water / vending machine'],
                'wifi' => ['fa-wifi', 'WiFi'],
                'airconditiona' => ['fa-temperature-low', 'Air conditioning'],
                'cafeteria' => ['fa-utensils', 'Cafeteria'],
            ];
            if ($amenities && is_array($amenities)) : ?>
                <article class="mp-court-info-block">
                    <h2>Court Amenities</h2>
                    <ul class="mp-court-amenities">
                        <?php foreach ($amenities as $amenity) :
                            if (empty($amenity_labels[$amenity])) continue;
                            [$icon, $label] = $amenity_labels[$amenity];
                            ?>
                            <li><i class="fa-solid <?php echo esc_attr($icon); ?>" aria-hidden="true"></i><?php echo esc_html($label); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endif; ?>

            <?php if ($address = get_field('address', $service_id)) : ?>
                <article class="mp-court-info-block">
                    <h2>Location</h2>
                    <p><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?php echo esc_html($address); ?></p>
                    <?php
                    $lat = get_field('address_lat', $service_id);
                    $lng = get_field('address_lang', $service_id);
                    if ($lat && $lng) : ?>
                        <div id="service-map" class="mp-court-map" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>"></div>
                    <?php endif; ?>
                </article>
            <?php endif; ?>

            <?php if ($additional_information = get_field('additional_information', $service_id)) : ?>
                <article class="mp-court-info-block mp-court-additional-information"><?php echo wp_kses_post($additional_information); ?></article>
            <?php endif; ?>
        </section>

        <section class="mp-court-reviews" id="review" aria-label="Reviews">
            <?php echo hash_display_rating_summary(); ?>
            <?php comments_template(); ?>
        </section>
    </div>
</main>

<div class="sharethis-share-buttons" data-type="sticky-share-buttons">
    <span data-network="facebook"></span><span data-network="twitter"></span><span data-network="linkedin"></span><span data-network="email"></span><span data-network="sharethis"></span>
</div>
<?php get_footer(); ?>
