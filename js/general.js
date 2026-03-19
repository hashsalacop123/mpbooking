(function($){

 
    $('#coach-search').select2({
        theme: 'bootstrap4',
        placeholder: 'Search coach...',
        minimumInputLength: 2,
        ajax: {
            url: booking_ajax.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    action: 'search_coaches',
                    nonce: booking_ajax.nonce
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    let selectedUrl = '';

    $('#coach-search').on('select2:select', function(e) {
        selectedUrl = e.params.data.id;
        // optional redirect
        // window.location.href = selectedUrl;
    });

    // button click
    $('#search-btn').on('click', function() {

        if (selectedUrl) {
            window.location.href = selectedUrl;
        } else {
            alert('Please select a result first');
        }

    });

  if ($.fn.slick) {
      Fancybox.bind('[data-fancybox="coach-gallery"]', {
          Thumbs: false,
          Toolbar: {
            display: [
              "close",
            ],
          },
        });
}
    if ($.fn.slick) {

      $('.slider-calendar').slick({
          slidesToShow: 3,
          slidesToScroll: 1,
          autoplay: false,
          autoplaySpeed: 2000,
          arrows: true,
          prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>',
          nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>',
                responsive: [
                {
                    breakpoint: 768, // 👈 767 and below
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('locationSearch');
    var cards = document.querySelectorAll('.marker-card');

    if (!searchInput || cards.length === 0) return;

    searchInput.addEventListener('input', function () {
        var searchValue = this.value.toLowerCase();

        cards.forEach(function (card) {
            var titleEl = card.querySelector('.card-title');

            if (!titleEl) return;

            var title = titleEl.textContent.toLowerCase();

            var wrapper = card.closest('.wrapper-area');
            if (!wrapper) return;

            if (title.includes(searchValue)) {
                wrapper.style.display = '';
            } else {
                wrapper.style.display = 'none';
            }
        });
    });
});


    // Open modal

    // Open modal
    $(document).on('click', '.open-booking-modal', function () {
jQuery('.open-booking-modal').length

        $('#modal-booking-id').val($(this).data('id'));
        $('#modal-name').val($(this).data('name'));
        $('#modal-email').val($(this).data('email'));
        $('#modal-date').val($(this).data('date'));
        $('#modal-start').val($(this).data('start'));
        $('#modal-end').val($(this).data('end'));
        $('#modal-amount').val($(this).data('amount'));
        $('#modal-status').val($(this).data('status'));

        $('#bookingModal').fadeIn();
    });

    // Close modal
    $(document).on('click', '#close-modal', function () {
        $('#bookingModal').fadeOut();
    });

    // Update booking
    $(document).on('click', '#update-booking', function () {

        
        var booking_id = $('#modal-booking-id').val();
        var status = $('#modal-status').val();

        $.post(booking_ajax.ajax_url, {
            action: 'update_booking_status',
            booking_id: booking_id,
            status: status,
            nonce: booking_ajax.nonce
        }, function (response) {

            if (response.success) {

                // Close modal
                $('#bookingModal').fadeOut();

                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Booking status updated successfully.'
                }).then(() => {
                    location.reload();
                });

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.data || 'Update failed.'
                });

            }
        });

    });

// user approval modal
// Open modal
$(document).on('click', '.open-user-modal', function () {

    $('#modal-user-id').val($(this).data('id'));
    $('#modal-user-first').val($(this).data('first'));
    $('#modal-user-last').val($(this).data('last'));
    $('#modal-user-status').val($(this).data('status'));

    $('#userModal').fadeIn();
});

// Close modal
$(document).on('click', '#close-user-modal', function () {
    $('#userModal').fadeOut();
});

// Update user status
$(document).on('click', '#update-user-membership', function () {

    var user_id = $('#modal-user-id').val();
    var status  = $('#modal-user-status').val();

    $.post(booking_ajax.ajax_url, {
        action: 'update_user_status',
        user_id: user_id,
        status: status,
        nonce: booking_ajax.nonce
    }, function (response) {

        if (response.success) {

            $('#userModal').fadeOut();

            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'User status updated successfully.'
            }).then(() => {
                location.reload();
            });

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.data || 'Update failed.'
            });

        }
    });

});

jQuery(document).ready(function ($) {

    $('#sampleAccordion').on('show.bs.collapse', function (e) {
        $(e.target)
            .prev('.card-header')
            .find('.toggle-icon')
            .removeClass('fa-plus')
            .addClass('fa-minus');
    });

    $('#sampleAccordion').on('hide.bs.collapse', function (e) {
        $(e.target)
            .prev('.card-header')
            .find('.toggle-icon')
            .removeClass('fa-minus')
            .addClass('fa-plus');
    });

});

    $('.menu-icon').click(function () {

        if ($('#navigator').css("right") == "-250px") {

            $('#navigator').animate({right: '0px'}, 350);
            $(this).animate({right: '120px'}, 350);
            $('.menu-text').animate({right: '300px'}, 350).text("Close");

        } else {

            $('#navigator').animate({right: '-250px'}, 350); 
            $(this).animate({right: '0px'}, 350);
            $('.menu-text').animate({right: '50px'}, 350).text("Menu");

        }

        $(this).toggleClass("on");

    });

    


})(jQuery);
