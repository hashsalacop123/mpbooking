(function($){
    $(document).ready(function(){

        const $acfAddressInput = $('#acf-field_695342a608250'); // address
        const $acfLatField     = $('#acf-field_695372c113ba9'); // latitude
        const $acfLngField     = $('#acf-field_695372d913baa'); // longitude

        if(!$acfAddressInput.length) return;

        mapboxgl.accessToken = mapData.token;

       let savedLat = parseFloat($acfLatField.val());
let savedLng = parseFloat($acfLngField.val());

let center = [123.8854, 10.3157]; // default Cebu

// If existing coordinates exist, use them
if(!isNaN(savedLat) && !isNaN(savedLng)){
    center = [savedLng, savedLat];
}

const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v12',
    center: center,
    zoom: 15
});

let marker;

// If we already have coordinates (Update page)
if(!isNaN(savedLat) && !isNaN(savedLng)){
    marker = new mapboxgl.Marker({draggable:true})
        .setLngLat(center)
        .addTo(map);

    addMarkerDragEvent(marker);
}

// Otherwise try geolocation (Add page)
else if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(
        function(position){
            const userCoords = [position.coords.longitude, position.coords.latitude];

            map.setCenter(userCoords);
            map.setZoom(16);

            marker = new mapboxgl.Marker({draggable:true})
                .setLngLat(userCoords)
                .addTo(map);

            updateFields(userCoords);
            addMarkerDragEvent(marker);
        },
        function(err){
            console.warn('Geolocation error: ' + err.message);
        }
    );
}

        const geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            mapboxgl: mapboxgl,
            countries: 'ph',
            types: 'address,place,locality,neighborhood,poi',
            fuzzyMatch: true,
            placeholder: 'Enter your Philippine address'
        });

        geocoder.addTo('#geocoder');
        $acfAddressInput.hide();

        geocoder.on('result', function(e){
            const coords = e.result.geometry.coordinates;

            if(marker) marker.remove();
            marker = new mapboxgl.Marker({draggable:true}).setLngLat(coords).addTo(map);
            addMarkerDragEvent(marker);

            updateFields(coords, e.result.place_name);
            map.flyTo({center: coords, zoom:16});
        });

        $(geocoder._inputEl).on('input', function(){
            $acfAddressInput.val(this.value).trigger('change');
        });

        function updateFields(coords, placeName){
            if(placeName){
                $acfAddressInput.val(placeName).trigger('change');
            }
            $acfLatField.val(coords[1]).trigger('change');
            $acfLngField.val(coords[0]).trigger('change');
        }

        function addMarkerDragEvent(marker){
            marker.on('dragend', function(){
                const lngLat = marker.getLngLat();
                // Reverse geocode the new coordinates
                fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${lngLat.lng},${lngLat.lat}.json?access_token=${mapboxgl.accessToken}&types=address,place,locality,neighborhood,poi&country=PH`)
                    .then(res => res.json())
                    .then(data => {
                        const placeName = data.features[0]?.place_name || '';
                        updateFields([lngLat.lng, lngLat.lat], placeName);
                    });
            });
        }

    });

// Use delegation so it works even if elements load late
$(document).on('click', '.marker-card', function() {
    const index = $(this).data('index');
    
    // Check if map and mapMarkers actually exist before trying to use them
    if (typeof map !== 'undefined' && typeof mapMarkers !== 'undefined' && mapMarkers[index]) {
        const markerObj = mapMarkers[index];

        // Fly to the coordinates
        map.flyTo({
            center: [markerObj.lng, markerObj.lat],
            zoom: 15,
            essential: true
        });

        // Automatically open the popup
        markerObj.marker.togglePopup();
    } else {
        console.warn("Map or Marker not ready yet.");
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.getElementById('service-map');
    if (!mapEl) return;

    const lat = mapEl.dataset.lat;
    const lng = mapEl.dataset.lng;

    mapboxgl.accessToken = mapData.token;

    const map = new mapboxgl.Map({
        container: 'service-map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [lng, lat],
        zoom: 14
    });

    new mapboxgl.Marker()
        .setLngLat([lng, lat])
        .addTo(map);
});

     $('.service-gallery-main').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: '.service-gallery-thumbs'
  });

  $('.service-gallery-thumbs').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    asNavFor: '.service-gallery-main',
    dots: false,
    arrows: false,
    focusOnSelect: true,
    centerMode: false
  });

  

})(jQuery);

