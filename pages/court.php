<?php 
// Template Name: Court
get_header(); ?>

<main class="general-filter-wrapper mp-court-finder">
    <div class = "container-fluid">
        <div class="row mp-court-finder-layout">
<?php
                    $sport_filter = isset($_GET['sport']) ? sanitize_key(wp_unslash($_GET['sport'])) : '';
                    $sport_genres = array(
                        'tennis'     => array('tennis-court'),
                        'pickleball' => array('pickleball-court'),
                        'padel'      => array('padel-court'),
                    );
                    $genre_terms = isset($sport_genres[$sport_filter])
                        ? $sport_genres[$sport_filter]
                        : array('pickleball-court', 'tennis-court');

                    $services = new WP_Query(array(
                        'post_type'      => 'service',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'tax_query'      => array(
                        array(
                            'taxonomy' => 'genre',
                            'field'    => 'slug',
                            'terms'    => $genre_terms,
                        ),
                    ),
                    ));
                    $markers = [];

                    if ( $services->have_posts() ) :
                        while ( $services->have_posts() ) : $services->the_post();
                            $lat = get_field('address_lat');
                            $lng = get_field('address_lang');

                            if ( $lat >= 9.5 && $lat <= 12.5 && $lng >= 123 && $lng <= 126 ) {

                                $about_me = get_field('about_me');
                                $about_clean = wp_strip_all_tags($about_me);
                                $about_trimmed = mb_substr($about_clean, 0, 100) . '...';
                                $featured = get_field('featured_image');
                                $court_name = get_field('court_name_gym');

                                $featured_url = '';
                                if ($featured && is_array($featured)) {
                                    $featured_url = $featured['url'];
                                }
                                // echo '<pre>';
                                //     var_dump($markers);
                                // echo '</pre>';

                                $markers[] = array(
                                    'title' => get_the_title(),
                                    'court_name' => $court_name,
                                    'lat'   => $lat,
                                    'lng'   => $lng,
                                    'phone' => get_field('phone'),
                                    'address' => get_field('address'),
                                    'about_me' => $about_trimmed,
                                    'featured_image' => $featured_url,
                                    'url'   => get_permalink()
                                );
                            }
                        endwhile;
                        wp_reset_postdata();
                    endif;

                ?>
                    <!-- INFORMATION COLUMN (now first / left) -->
            <aside class="col-xl-4 col-lg-4 col-md-12 col-sm-12 sidebar-maps mp-court-finder-sidebar">
                <!-- INFORMATION -->
                <!-- SLIDER SLICK  -->
                <div class="container-data-convice mp-court-finder-panel">
                    <div class="row-power">
                         <div class="text-paragraph mp-court-finder-heading">
                            <span class="mp-court-finder-eyebrow">MatchPoint courts</span>
                            <h1>Find a court</h1>
                            <p>Explore nearby courts and choose a location that fits your game.</p>
                            <label for="locationSearch">Search by location</label>
                            <div class="mp-court-search-field">
                                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                <input type="text" id="locationSearch" placeholder="Enter an address" class="form-control autosearch" value="" />
                            </div>
                            <div class="mp-court-result-count">
                                <strong><?php echo esc_html(count($markers)); ?></strong>
                                <span><?php echo count($markers) === 1 ? 'court available' : 'courts available'; ?></span>
                            </div>
                        </div>

                    <div class="location-wrapper">

                        <?php if (empty($markers)) : ?>
                            <div class="mp-court-finder-empty">No courts are listed in this area yet.</div>
                        <?php else : foreach ($markers as $index => $marker): ?>
                            <div class="col-md-12 mb-12 wrapper-area">
                                <div class="card marker-card" data-index="<?php echo $index; ?>" style="cursor:pointer;">
                                    <?php if($marker['featured_image']){ ?>
                                        <img src="<?php echo esc_url($marker['featured_image']); ?>" class="card-img-top" alt="<?php echo esc_attr($marker['court_name'] ?: $marker['title']); ?>">
                                    <?php } else {
                                        echo '<img src="' . esc_url(get_template_directory_uri() . '/img/placeholder-400x400.jpg') . '" class="card-img-top" alt="Court photo placeholder">';

                                    }?>
                                    <div class="card-body">
                                        <?php 
                                                if($marker['court_name']) {
                                                    echo '<h3>'.esc_html($marker['court_name']).'</h3>';
                                                }else {
                                                    echo '<h3>'.esc_html($marker['title']).'</h3>';
                                                }
                                        ?>
                                        <h5 class="card-title"><?php echo esc_html($marker['address']); ?></h5>
                                        <!-- <p class="card-text"><?php echo esc_html($marker['about_me']); ?></p> -->

                    <div class="court-review-wrapper">
                                            <?php echo hash_display_rating_summary(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                                            <h4 class="mp-court-finder-hint">Select a court to see it on the map.</h4>

                    </div>
                </div>
            </aside>

            <!-- MAP COLUMN (now second / right) -->
            <section class="col-xl-8 col-lg-8 col-md-12 col-sm-12 container-map mp-court-map-panel" aria-label="Court locations map">
                <!-- MAPS -->
                <div class="mp-court-map-label"><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i> Court locations</div>
                <div id="map" aria-label="Map of nearby courts"></div>
                <script>
                    const markers = <?php echo json_encode($markers); ?>;
                </script>
                    
                <script>
                    var map; 
                    var mapMarkers = []; 

                    window.addEventListener('load', function() {
                        if (typeof mapboxgl === 'undefined') return;

mapboxgl.accessToken = "<?php echo esc_js(MAPBOX_TOKEN); ?>";
                        
                        map = new mapboxgl.Map({
                            container: 'map',
                            style: 'mapbox://styles/mapbox/streets-v11',
                            center: [123.8854, 10.3157], 
                            zoom: 10
                        });

                        const markersData = <?php echo json_encode($markers); ?>;

                        markersData.forEach((marker, index) => {
                            const lng = parseFloat(marker.lng);
                            const lat = parseFloat(marker.lat);

                            if (!isNaN(lng) && !isNaN(lat)) {
                                const m = new mapboxgl.Marker()
                                    .setLngLat([lng, lat])
.setPopup(
    new mapboxgl.Popup().setHTML(`
        <div class="popup-content">
         <h6>${marker.court_name || marker.title}</h6>     
            ${marker.address ? `<p><strong>Address:</strong> ${marker.address}</p>` : ''}
            ${marker.phone ? `<p><strong>Phone:</strong> ${marker.phone}</p>` : ''}
            ${marker.about_me ? `<p>${marker.about_me}</p>` : ''}
            ${marker.url ? `<a href="${marker.url}" class="btn btn-sm btn-primary btn-general">View Service</a>` : ''}
        </div>
    `)
)
                                    .addTo(map);

                                mapMarkers[index] = { 
                                    marker: m, 
                                    lng: lng, 
                                    lat: lat 
                                };
                            }
                        });
                    });
                </script>
            </section>

        </div>
    </div>
</main>



<?php get_footer(); ?>
