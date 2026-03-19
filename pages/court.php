<?php 
// Template Name: Court
get_header(); ?>

<div class = "general-filter-wrapper">
    <div class = "container-fluid">
        <div class = "row">
 <?php
                    $services = new WP_Query(array(
                        'post_type'      => 'service',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'tax_query'      => array(
                        array(
                            'taxonomy' => 'genre',
                            'field'    => 'slug',
                            'terms'    => array('pickleball-court', 'tennis-court'),
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
                                $court_name = get_field('court_name');

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
            <div class = "col-xl-4 col-lg-4 col-md-12 co-sm-12 sidebar-maps">
                <!-- INFORMATION -->
                <!-- SLIDER SLICK  -->
                <div class="container-data-convice">
                    <div class="row-power">
                         <div class="text-paragraph">
                            <h1>Find location</h1>
                            <input type = "text" id="locationSearch" placeholder = "search your court"  class="form-control autosearch" value="" />
                        </div>

                    <div class="location-wrapper">

                        <?php foreach ($markers as $index => $marker): ?>
                            <div class="col-md-12 mb-12 wrapper-area">
                                <div class="card marker-card" data-index="<?php echo $index; ?>" style="cursor:pointer;">
                                    <?php if($marker['featured_image']){ ?>
                                        <img src="<?php echo esc_url($marker['featured_image']); ?>" class="card-img-top">
                                    <?php } else {
                                        echo '<img src = "'.get_template_directory_uri() . '/img/placeholder-400x400.jpg'.'" class = "card-image-top">';

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
                                        
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                                            <h4>Scrool Left/Right</h4>

                    </div>
                </div>
            </div>

            <!-- MAP COLUMN (now second / right) -->
            <div class = "col-xl-8 col-lg-8 col-md-12 col-sm-12 container-map">
                <!-- MAPS -->
     
      <div id="map"></div> 
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
            </div>

        </div>
    </div>
</div>



<?php get_footer(); ?>