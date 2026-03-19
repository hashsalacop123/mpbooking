(function($) {
    "use strict";

    $(document).ready(function() {
        
        let currentDaySlots = []; 
        // Grab the hourly rate from the <ul> attribute
        // We use parseFloat to turn the string "500" into a number 500
        const hourlyRate = parseFloat($('.slider-calendar').data('rate')) || 0;

        $(document).on('click', '.availability', function() {
            const selectedTime = $(this).data('time');
            const selectedDate = $(this).data('date'); // Grab the date from the clicked <li>
            const $container = $(this).closest('.day-parent'); 
            const rawSlots = $container.find('.day-slots').val();

            if (!rawSlots) return;

            try {
                currentDaySlots = JSON.parse(rawSlots);

                $('#booking_start').empty();
                currentDaySlots.forEach(function(slot) {
                    $('#booking_start').append(`<option value="${slot}">${slot}</option>`);
                });

                // Set the values
                $('#booking_start').val(selectedTime);
                $('#selected_date').val(selectedDate); // Make sure you have <input type="hidden" id="selected_date"> in your modal

                updateEndTimeDropdown(selectedTime);
                $('#bookingModal').modal('show');
                
            } catch (e) { console.error("JSON Parse Error", e); }
        });

        // RE-CALCULATE when Start Time changes
        $('#booking_start').on('change', function() {
            updateEndTimeDropdown($(this).val());
        });

        // RE-CALCULATE when End Time changes
        $(document).on('change', '#booking_end', function() {
            calculateTotal();
        });

        function updateEndTimeDropdown(startTime) {
            const $endTimeSelect = $('#booking_end');
            $endTimeSelect.empty();

            const startIndex = currentDaySlots.indexOf(startTime);

            for (let i = startIndex + 1; i < currentDaySlots.length; i++) {
                $endTimeSelect.append(`<option value="${currentDaySlots[i]}">${currentDaySlots[i]}</option>`);
            }

            if ($endTimeSelect.children().length === 0) {
                $endTimeSelect.append(`<option value="${startTime}">${startTime}</option>`);
            }

            // Run calculation immediately after updating dropdown
            calculateTotal();
        }

        function calculateTotal() {
            const startTime = $('#booking_start').val();
            const endTime = $('#booking_end').val();
            
            const startIndex = currentDaySlots.indexOf(startTime);
            const endIndex = currentDaySlots.indexOf(endTime);

            // If user picks 5am to 7am: index 2 - index 0 = 2 hours
            let hours = endIndex - startIndex;
            
            // Fallback for last slot
            if (hours <= 0) hours = 1;

            const total = hours * hourlyRate;

            // Update the amount field (using clean number for the input value)
            $('#amount').val(total.toFixed(2));
            
            // Optional: If you want to show the Peso sign in a label instead of the input:
            // $('.total-label').text('₱' + total.toLocaleString());
        }

 $('#confirm_booking_btn').on('click', function(e) {
    e.preventDefault();
    
    const bookingData = {
        action: 'handle_coach_booking',
        name: $('#booking_name').val(),
        email: $('#booking_email').val(),
        start: $('#booking_start').val(),
        end: $('#booking_end').val(),
        date: $('#selected_date').val(), 
        comment: $('#booking_comment').val(),
        amount: $('#amount').val(),
        coach_id: coachBookingData.coach_id
    };

    console.log(bookingData);

    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: bookingData,
        beforeSend: function() {
            $('#confirm_booking_btn').prop('disabled', true).text('Redirecting to GCash...');
        },
success: function(response) {

    console.log('AJAX response:', response);

    if (response.success && response.data && response.data.checkout_url) {

        // Open PayMongo in new tab
        window.open(response.data.checkout_url, '_blank');

        // Change button text
        $('#confirm_booking_btn').text('Waiting for payment...');

        // Refresh page after a few seconds
        setTimeout(function(){
            location.reload();
        }, 15000); // 15 seconds

    } else {
        alert('Payment link not returned.');
        $('#confirm_booking_btn').prop('disabled', false).text('Confirm Booking');
    }
},
        error: function() {
            alert('Error submitting booking.');
            $('#confirm_booking_btn').prop('disabled', false).text('Confirm Booking');
        }
    });
});
        
    });
})(jQuery);