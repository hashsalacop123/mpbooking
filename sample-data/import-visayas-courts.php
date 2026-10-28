<?php
if (!defined('WP_CLI') || !WP_CLI) {
    http_response_code(404);
    exit;
}

if (!function_exists('update_field')) {
    WP_CLI::error('Advanced Custom Fields must be active before importing the sample courts.');
}

if (!post_type_exists('service') || !taxonomy_exists('genre')) {
    WP_CLI::error('The service post type and genre taxonomy must be registered before importing.');
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$admins = get_users([
    'role'   => 'administrator',
    'number' => 1,
]);

if (!$admins) {
    WP_CLI::error('Create an administrator account before importing the sample courts.');
}

$term = term_exists('pickleball-court', 'genre');
if (!$term) {
    $term = wp_insert_term('Pickleball Court', 'genre', ['slug' => 'pickleball-court']);
}

if (is_wp_error($term)) {
    WP_CLI::error('Unable to create the pickleball-court genre: ' . $term->get_error_message());
}

$term_id = (int) (is_array($term) ? $term['term_id'] : $term);
$photos = [
    [
        'url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/1/19/Aerial_Pickleball_Courts.jpg/1280px-Aerial_Pickleball_Courts.jpg',
        'alt' => 'Aerial view of outdoor pickleball courts',
        'credit' => 'Aerial Pickleball Courts by TheVillagesFL, Wikimedia Commons, CC BY-SA 4.0 (https://commons.wikimedia.org/wiki/File:Aerial_Pickleball_Courts.jpg)',
        'license' => 'https://creativecommons.org/licenses/by-sa/4.0/',
    ],
    [
        'url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/c/c6/Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_1-3.jpg/1280px-Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_1-3.jpg',
        'alt' => 'Outdoor pickleball courts with blue playing surfaces',
        'credit' => 'Harry B. Anderson Tennis Center - Pickleball Courts 1-3 by GA Kevin, Wikimedia Commons, CC0 (https://commons.wikimedia.org/wiki/File:Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_1-3.jpg)',
        'license' => 'https://creativecommons.org/publicdomain/zero/1.0/',
    ],
    [
        'url' => 'https://upload.wikimedia.org/wikipedia/commons/f/f7/Pickleball_Pros.jpg',
        'alt' => 'Pickleball players playing on a court',
        'credit' => 'Pickleball Pros by Picklerpeej, Wikimedia Commons, CC BY-SA 4.0 (https://commons.wikimedia.org/wiki/File:Pickleball_Pros.jpg)',
        'license' => 'https://creativecommons.org/licenses/by-sa/4.0/',
    ],
    [
        'url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/2/25/Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_10-12.jpg/1280px-Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_10-12.jpg',
        'alt' => 'A row of outdoor pickleball courts',
        'credit' => 'Harry B. Anderson Tennis Center - Pickleball Courts 10-12 by GA Kevin, Wikimedia Commons, CC0 (https://commons.wikimedia.org/wiki/File:Harry_B._Anderson_Tennis_Center_-_Pickleball_Courts_10-12.jpg)',
        'license' => 'https://creativecommons.org/publicdomain/zero/1.0/',
    ],
];
$records = [
    [
        'import_id' => 'visayas-demo-cebu-city',
        'name' => 'DEMO: Cebu City Pickleball Courts',
        'address' => 'Sample location pin: Cebu City, Cebu, Central Visayas, Philippines',
        'latitude' => '10.3157',
        'longitude' => '123.8854',
        'rate' => 600,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'waitingarea', 'drinkingwaterorvending'],
        'email' => 'cebu-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Cebu City, Central Visayas. This profile demonstrates sample pickleball courts, rates, and daily booking availability.',
    ],
    [
        'import_id' => 'visayas-demo-lapu-lapu',
        'name' => 'DEMO: Lapu-Lapu City Pickleball Courts',
        'address' => 'Sample location pin: Lapu-Lapu City, Cebu, Central Visayas, Philippines',
        'latitude' => '10.3103',
        'longitude' => '123.9494',
        'rate' => 700,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'wifi', 'drinkingwaterorvending'],
        'email' => 'lapulapu-demo@example.com',
        'about' => 'Fictional sample outdoor pickleball venue in Lapu-Lapu City, Cebu. Its approximate map pin demonstrates a Visayas pickleball listing.',
    ],
    [
        'import_id' => 'visayas-demo-tagbilaran',
        'name' => 'DEMO: Tagbilaran City Pickleball Courts',
        'address' => 'Sample location pin: Tagbilaran City, Bohol, Central Visayas, Philippines',
        'latitude' => '9.6474',
        'longitude' => '123.8554',
        'rate' => 500,
        'surface' => 'hardcourt',
        'amenities' => ['restrooms', 'waitingarea', 'drinkingwaterorvending'],
        'email' => 'tagbilaran-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Tagbilaran City, Bohol, Central Visayas, demonstrating a complete court profile and booking calendar.',
    ],
    [
        'import_id' => 'visayas-demo-mandaue',
        'name' => 'DEMO: Mandaue City Pickleball Courts',
        'address' => 'Sample location pin: Mandaue City, Cebu, Central Visayas, Philippines',
        'latitude' => '10.3236',
        'longitude' => '123.9220',
        'rate' => 550,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'wifi', 'waitingarea'],
        'email' => 'mandaue-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Mandaue City, Cebu, with two sample courts and daily booking hours.',
    ],
    [
        'import_id' => 'visayas-demo-talisay',
        'name' => 'DEMO: Talisay City Pickleball Courts',
        'address' => 'Sample location pin: Talisay City, Cebu, Central Visayas, Philippines',
        'latitude' => '10.2447',
        'longitude' => '123.8490',
        'rate' => 500,
        'surface' => 'rubberized',
        'amenities' => ['parkingarea', 'restrooms', 'drinkingwaterorvending'],
        'email' => 'talisay-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Talisay City, Cebu. Replace the sample location, rates, and facilities before publishing.',
    ],
    [
        'import_id' => 'visayas-demo-danao',
        'name' => 'DEMO: Danao City Pickleball Courts',
        'address' => 'Sample location pin: Danao City, Cebu, Central Visayas, Philippines',
        'latitude' => '10.5202',
        'longitude' => '124.0270',
        'rate' => 450,
        'surface' => 'hardcourt',
        'amenities' => ['restrooms', 'waitingarea', 'drinkingwaterorvending'],
        'email' => 'danao-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Danao City, Cebu, intended to demonstrate map and schedule listings.',
    ],
    [
        'import_id' => 'visayas-demo-carcar',
        'name' => 'DEMO: Carcar City Pickleball Courts',
        'address' => 'Sample location pin: Carcar City, Cebu, Central Visayas, Philippines',
        'latitude' => '10.1061',
        'longitude' => '123.6400',
        'rate' => 400,
        'surface' => 'rubberized',
        'amenities' => ['parkingarea', 'restrooms', 'waitingarea'],
        'email' => 'carcar-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Carcar City, Cebu, with example rates and amenities for local testing.',
    ],
    [
        'import_id' => 'visayas-demo-toledo',
        'name' => 'DEMO: Toledo City Pickleball Courts',
        'address' => 'Sample location pin: Toledo City, Cebu, Central Visayas, Philippines',
        'latitude' => '10.3770',
        'longitude' => '123.6380',
        'rate' => 450,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'drinkingwaterorvending'],
        'email' => 'toledo-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Toledo City, Cebu. Its map location is an approximate city-center pin.',
    ],
    [
        'import_id' => 'visayas-demo-bogo',
        'name' => 'DEMO: Bogo City Pickleball Courts',
        'address' => 'Sample location pin: Bogo City, Cebu, Central Visayas, Philippines',
        'latitude' => '11.0510',
        'longitude' => '124.0060',
        'rate' => 500,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'waitingarea', 'wifi'],
        'email' => 'bogo-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Bogo City, Cebu, for demonstrating a complete court profile.',
    ],
    [
        'import_id' => 'visayas-demo-cordova',
        'name' => 'DEMO: Cordova Pickleball Courts',
        'address' => 'Sample location pin: Cordova, Cebu, Central Visayas, Philippines',
        'latitude' => '10.2500',
        'longitude' => '123.9500',
        'rate' => 550,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'drinkingwaterorvending'],
        'email' => 'cordova-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Cordova, Cebu, with two example courts and daily opening hours.',
    ],
    [
        'import_id' => 'visayas-demo-panglao',
        'name' => 'DEMO: Panglao Pickleball Courts',
        'address' => 'Sample location pin: Panglao, Bohol, Central Visayas, Philippines',
        'latitude' => '9.5780',
        'longitude' => '123.7450',
        'rate' => 650,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'wifi', 'waitingarea'],
        'email' => 'panglao-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Panglao, Bohol. The map pin is approximate and should not be treated as a real venue address.',
    ],
    [
        'import_id' => 'visayas-demo-ormoc',
        'name' => 'DEMO: Ormoc City Pickleball Courts',
        'address' => 'Sample location pin: Ormoc City, Leyte, Eastern Visayas, Philippines',
        'latitude' => '11.0060',
        'longitude' => '124.6070',
        'rate' => 500,
        'surface' => 'hardcourt',
        'amenities' => ['parkingarea', 'restrooms', 'waitingarea', 'drinkingwaterorvending'],
        'email' => 'ormoc-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Ormoc City, Leyte, demonstrating a Visayas listing outside Cebu and Bohol.',
    ],
    [
        'import_id' => 'visayas-demo-tacloban',
        'name' => 'DEMO: Tacloban City Pickleball Courts',
        'address' => 'Sample location pin: Tacloban City, Leyte, Eastern Visayas, Philippines',
        'latitude' => '11.2440',
        'longitude' => '125.0030',
        'rate' => 600,
        'surface' => 'rubberized',
        'amenities' => ['parkingarea', 'restrooms', 'wifi', 'drinkingwaterorvending'],
        'email' => 'tacloban-demo@example.com',
        'about' => 'Fictional sample pickleball venue in Tacloban City, Leyte. This listing is for local testing and needs verified venue details before publication.',
    ],
];

$timezone = new DateTimeZone('Asia/Manila');
$utc = new DateTimeZone('UTC');

foreach ($records as $record_index => $record) {
    $existing = get_posts([
        'post_type'      => 'service',
        'post_status'    => ['draft', 'pending', 'publish', 'private', 'trash'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_mp_sample_import_id',
        'meta_value'     => $record['import_id'],
    ]);

    if ($existing) {
        $post_id = (int) $existing[0];
        WP_CLI::log('Updating sample listing: ' . $record['name']);
    } else {
        $post_id = wp_insert_post([
            'post_type'      => 'service',
            'post_status'    => 'draft',
            'post_title'     => $record['name'],
            'post_content'   => $record['about'] . ' Sample listing only: venue information, prices, and contacts are placeholders. Replace them before publishing.',
            'post_excerpt'   => $record['about'],
            'post_author'    => (int) $admins[0]->ID,
            'comment_status' => 'closed',
        ], true);

        if (is_wp_error($post_id)) {
            WP_CLI::error('Unable to create ' . $record['name'] . ': ' . $post_id->get_error_message());
        }

        if (!add_post_meta($post_id, '_mp_sample_import_id', $record['import_id'], true)) {
            wp_delete_post($post_id, true);
            WP_CLI::error('Unable to mark the sample listing for repeat-safe imports: ' . $record['name']);
        }
    }

    $gallery_ids = [];
    $photo_rotation = $record_index % count($photos);
    $listing_photos = array_merge(
        array_slice($photos, $photo_rotation),
        array_slice($photos, 0, $photo_rotation)
    );

    foreach ($listing_photos as $photo_index => $photo) {
        $photo_marker = $record['import_id'] . '-pickleball-photo-' . ($photo_index + 1);
        $saved_photo = get_posts([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_key'       => '_mp_sample_photo_import_id',
            'meta_value'     => $photo_marker,
        ]);

        if ($saved_photo) {
            $attachment_id = (int) $saved_photo[0];
        } else {
            $temporary_file = download_url($photo['url'], 30);
            if (is_wp_error($temporary_file)) {
                WP_CLI::error('Unable to download photo ' . ($photo_index + 1) . ' for ' . $record['name'] . ': ' . $temporary_file->get_error_message());
            }

            $attachment_id = media_handle_sideload([
                'name'     => $record['import_id'] . '-pickleball-photo-' . ($photo_index + 1) . '.jpg',
                'tmp_name' => $temporary_file,
            ], $post_id, $photo['credit']);

            if (is_wp_error($attachment_id)) {
                if (file_exists($temporary_file)) {
                    unlink($temporary_file);
                }
                WP_CLI::error('Unable to add photo ' . ($photo_index + 1) . ' for ' . $record['name'] . ': ' . $attachment_id->get_error_message());
            }

            if (!add_post_meta($attachment_id, '_mp_sample_photo_import_id', $photo_marker, true)) {
                WP_CLI::error('Unable to mark photo ' . ($photo_index + 1) . ' for repeat-safe imports: ' . $record['name']);
            }
        }

        update_post_meta($attachment_id, '_wp_attachment_image_alt', $photo['alt']);
        $attachment_update = wp_update_post([
            'ID'           => $attachment_id,
            'post_excerpt' => $photo['credit'] . '. License: ' . $photo['license'],
        ], true);
        if (is_wp_error($attachment_update) || !$attachment_update) {
            $error = is_wp_error($attachment_update) ? $attachment_update->get_error_message() : 'WordPress did not update the attachment.';
            WP_CLI::error('Unable to save photo attribution for ' . $record['name'] . ': ' . $error);
        }
        $gallery_ids[] = (int) $attachment_id;
    }

    $calendar = [];
    $first_day = (new DateTimeImmutable('today', $timezone))->modify('+1 day');
    for ($day_offset = 0; $day_offset < 180; $day_offset++) {
        $day = $first_day->modify('+' . $day_offset . ' days');
        $start = $day->setTime(6, 0)->setTimezone($utc);
        $end = $day->setTime(22, 0)->setTimezone($utc);
        $calendar[] = [
            'start' => $start->format('Y-m-d\TH:i:s\Z'),
            'end'   => $end->format('Y-m-d\TH:i:s\Z'),
        ];
    }

    $field_values = [
        'court_name_gym' => $record['name'],
        'featured_image' => $gallery_ids[0],
        'amenities' => $record['amenities'],
        'address' => $record['address'],
        'address_lat' => $record['latitude'],
        'address_lang' => $record['longitude'],
        'email' => $record['email'],
        'website' => 'https://example.com/',
        'images' => $gallery_ids,
        'about_me' => $record['about'] . ' Sample listing only: venue details and contact information are placeholders. Replace them before publishing.',
        'are_you' => [$term_id],
        'additional_information' => '<p>DEMO LISTING. This venue is fictional. Confirm the real venue, address, rates, opening hours, contact details, and facilities before publishing.</p><p>Location coordinates are approximate city-center pins for testing the Visayas map.</p>',
        'hourly_rate' => '₱' . number_format_i18n($record['rate']) . ' - ₱' . number_format_i18n($record['rate'] + 150),
        'avalability' => 'Daily, 6:00 AM to 10:00 PM (sample availability for the next 180 days; confirm before publishing).',
        'court_name' => [
            [
                'courth_name_number' => 'Pickleball Court 1',
                'court_calendar' => wp_json_encode($calendar),
                'rate' => (string) $record['rate'],
                'court_type' => 'outdoor',
                'surface_type' => $record['surface'],
                'court_size' => 'standard',
                'lighting_availability' => ['day', 'nightplay'],
            ],
            [
                'courth_name_number' => 'Pickleball Court 2',
                'court_calendar' => wp_json_encode($calendar),
                'rate' => (string) ($record['rate'] + 150),
                'court_type' => 'outdoor',
                'surface_type' => 'hardcourt',
                'court_size' => 'standard',
                'lighting_availability' => ['day', 'nightplay'],
            ],
        ],
    ];

    foreach ($field_values as $field_name => $field_value) {
        update_field($field_name, $field_value, $post_id);
    }

    $post_update = wp_update_post([
        'ID'         => $post_id,
        'post_title' => $record['name'],
        'post_content' => $record['about'] . ' Sample listing only: venue information, prices, and contacts are placeholders. Replace them before publishing.',
        'post_excerpt' => $record['about'],
    ], true);
    if (is_wp_error($post_update) || !$post_update) {
        $error = is_wp_error($post_update) ? $post_update->get_error_message() : 'WordPress did not update the listing.';
        WP_CLI::error('Unable to update ' . $record['name'] . ': ' . $error);
    }

    $assigned_terms = wp_set_object_terms($post_id, [$term_id], 'genre');
    if (is_wp_error($assigned_terms)) {
        wp_delete_attachment((int) $attachment_id, true);
        wp_delete_post($post_id, true);
        WP_CLI::error('Unable to assign the pickleball-court genre to ' . $record['name'] . ': ' . $assigned_terms->get_error_message());
    }

    WP_CLI::success('Imported sample listing "' . $record['name'] . '" with ' . count($gallery_ids) . ' pickleball photos and ' . count($calendar) . ' daily availability windows for two courts.');
}

WP_CLI::success('Visayas pickleball import finished. Review each draft and replace all sample details before publishing. The 13 listings have 4 pickleball photos each, 2 courts, and daily availability from 6:00 AM to 10:00 PM for the next 180 days.');
