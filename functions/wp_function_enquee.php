<?php
// -------------------------------
// Enqueue Theme Styles & Scripts
// -------------------------------
function theme_enqueue_styles() {
    $parent_style = 'parent-style';

    // Fonts & Icons
    wp_enqueue_style(
        'fontawesome-5',
        'https://use.fontawesome.com/releases/v5.15.4/css/all.css',
        [],
        '5.15.4'
    );
        wp_enqueue_style(
        'fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        [],
        '6.5.0'
    );

    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap'
    );

    // Slick, Bootstrap, Mapbox, Theme CSS
    wp_enqueue_style('slick-slider','//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('bootstrap','https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('mapbox-gl','https://api.mapbox.com/mapbox-gl-js/v3.17.0/mapbox-gl.css');
    wp_enqueue_style('mapbox-gl-geocoder','https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.css');
    wp_enqueue_style('mapbox-gl-directions','https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.3.1/mapbox-gl-directions.css');
    wp_enqueue_style('fancy-box','https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css');

    // Theme CSS
    wp_enqueue_style('landing-page', get_template_directory_uri() . '/css/home-page.css');
    wp_enqueue_style('innerpages', get_template_directory_uri() . '/css/inner-pages.css');
    wp_enqueue_style('global-css', get_template_directory_uri() . '/css/style.css');
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('responsive', get_template_directory_uri() . '/css/responsive.css');

    // jQuery
    wp_enqueue_script('jquery');

    // JS Libraries
    // Replace the old Alpha 6 link with this stable 4.6 link
    if ( is_page('dashboard') || is_page_template('template-dashboard.php') || is_page() ) {

                
                    // DataTables CSS
                    wp_enqueue_style(
                        'datatables-css',
                        'https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css',
                        array(),
                        '2.3.7'
                    );

                    // DataTables JS
                    wp_enqueue_script(
                        'datatables-js',
                        'https://cdn.datatables.net/2.3.7/js/dataTables.min.js',
                        array('jquery'), // dependency
                        '2.3.7',
                        true // load in footer
                    );
                wp_enqueue_script(
                    'datatables-responsive-js',
                    'https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js',
                    array('datatables-js'),
                    '3.0.2',
                    true
                );
                    // Inline initialization script
            $datatable_init = "
                Object.assign(DataTable.defaults, {
                    searching: false,
                    ordering: false
                });

                new DataTable('#bookings', {
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [
                        { responsivePriority: 1, targets: 0 }, // Name
                        { responsivePriority: 2, targets: 3 }, // Status (adjust if needed)
                        { responsivePriority: 3, targets: -1 } // Action
                    ]
                });

                new DataTable('#users-table', {
                    responsive: true,
                    autoWidth: false
                });

                console.log('Responsive:', DataTable.Responsive);
            ";

                wp_add_inline_script('datatables-responsive-js', $datatable_init);
                    }
                // Dashboard script
                

    wp_enqueue_style('bootstrap','https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js','https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', ['jquery'], null, true);
        wp_enqueue_script(
            'sweetalert',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            array(),
            null,
            true
        );   
     wp_enqueue_script('mapbox-gl-js','https://api.mapbox.com/mapbox-gl-js/v3.17.0/mapbox-gl.js', ['jquery'], null, false);
    wp_enqueue_script('slick-slider-js','//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], null, true);
    wp_enqueue_script('geocoder-js','https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js', ['jquery'], null, true);
    wp_enqueue_script('mapbox-gl-directions-js','https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.3.1/mapbox-gl-directions.js', ['jquery'], null, true);
    // wp_enqueue_script('bootstrap-js','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js', ['jquery'], null, true);
    wp_enqueue_script('fancy-box','https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js', ['jquery'], null, true);
    wp_enqueue_script('share','https://platform-api.sharethis.com/js/sharethis.js', ['jquery'], null, true);

    // Theme JS
    wp_enqueue_script('user-js', get_stylesheet_directory_uri() . '/js/user.js', ['jquery'], null, true);
    wp_enqueue_script('general-script',get_stylesheet_directory_uri() . '/js/general.js',['jquery','sweetalert','select2-js'],null,true);
    wp_enqueue_script('jquery-script', get_stylesheet_directory_uri() . '/js/jquery-script.js', ['jquery'], null, true);

    wp_localize_script(
    'jquery-script',
    'mapData',
    array(
        'token' => MAPBOX_TOKEN
    )
);
  wp_localize_script('general-script', 'booking_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('booking_nonce')
    ]);


}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

// -------------------------------
// ACF Frontend Styles & Media
// -------------------------------
function my_acf_enqueue_frontend_styles() {
    if( function_exists('acf_form_head') ) {
        wp_enqueue_style('acf-global'); // Load ACF frontend CSS
    }
}
add_action('wp_enqueue_scripts', 'my_acf_enqueue_frontend_styles');

function enqueue_acf_media_frontend() {
    if ( is_page('Add') ) { // Or any condition where ACF uploader is needed
        acf_enqueue_uploader();
    }
}
add_action('wp_enqueue_scripts', 'enqueue_acf_media_frontend');

// -------------------------------
// FullCalendar & Availability JS
// -------------------------------
function enqueue_availability_calendar() {
    // FullCalendar CSS & JS
    wp_enqueue_style('fullcalendar-css','https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css');
    wp_enqueue_script('fullcalendar','https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js',['jquery'],null,true);

    // Your availability JS
    wp_enqueue_script('availability-calendar', get_stylesheet_directory_uri() . '/js/coach-availability-calendar.js', ['jquery','fullcalendar'], null, true);
    wp_enqueue_script('service-availability-calendar', get_stylesheet_directory_uri() . '/js/service-availability-calendar.js', ['jquery','fullcalendar'], null, true);

    // Note: DO NOT localize here; localize in template after $coach_post_id is defined
}
add_action('wp_enqueue_scripts','enqueue_availability_calendar');

function enqueue_coach_booking_calendar() {

          // Select2 CSS
        wp_enqueue_style(
            'select2-css',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            [],
            '4.1.0'
        );

        // Optional Bootstrap theme
        wp_enqueue_style(
            'select2-bootstrap',
            'https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css',
            ['select2-css'],
            '1.5.2'
        );

                // Select2 JS (depends on jQuery)
                // JS
        wp_enqueue_script(
            'select2-js',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js',
            ['jquery'],
            null,
            true
        );
    
if ( ! is_singular( ['service','coach'] ) ) return;
    wp_enqueue_style(
        'fullcalendar-css',
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css'
    );

    wp_enqueue_script(
        'fullcalendar-js',
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'coach-booking-calendar',
        get_stylesheet_directory_uri() . '/js/coach-booking-calendar.js',
        ['jquery', 'fullcalendar-js'],
        '1.0',
        true
    );

 wp_localize_script('coach-booking-calendar', 'bookingData', [
    'ajaxurl'  => admin_url('admin-ajax.php'),
    'coach_id' => get_the_ID(),
    'nonce'    => wp_create_nonce('booking_nonce')
]);
wp_localize_script('service-booking-calendar', 'bookingData', [
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('booking_nonce')
]);
/**
 * Pass AJAX URL to JS
 */
  
  wp_enqueue_script(
        'service-booking-calendar',
        get_stylesheet_directory_uri() . '/js/service-booking-calendar.js',
        ['jquery', 'fullcalendar-js'],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_coach_booking_calendar');

?>
