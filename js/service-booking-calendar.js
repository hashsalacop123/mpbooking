(function($) {
"use strict";

$(document).ready(function() {

    let currentDaySlots = [];
    let selectedCourt = null;
    let currentRate = 0;
    let currentServiceId = null;
    let blockedTimes = {};

    /*
    |--------------------------------------------------------------------------
    | CLICK SLOT
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.availability-service', function() {

        const $day = $(this).closest('.day-parent');

        // ✅ BUILD BLOCKED TIMES (same as coach)
        blockedTimes = {};
        $day.find('.availability-service.pending, .availability-service.booked').each(function() {
            blockedTimes[$(this).data('time')] = true;
        });

        const selectedTime = $(this).data('time');
        const selectedDate = $(this).data('date');

        selectedCourt = $(this).data('court');

        // ✅ RATE
        currentRate = parseFloat($(this).attr('data-rate')) || 0;

        // ✅ SERVICE ID
        currentServiceId = $(this)
            .closest('.slider-calendar')
            .data('service');

        const rawSlots = $day.find('.day-slots').val();
        if (!rawSlots) return;

        currentDaySlots = JSON.parse(rawSlots);

        const startIndex = currentDaySlots.indexOf(selectedTime);
        const lastIndex  = currentDaySlots.length - 1;

        $('#booking_start').empty();
        $('#booking_end').empty();

        // 🚫 BLOCK IF CLICKED SLOT IS BLOCKED
        if (blockedTimes[selectedTime]) return;

        /*
        |--------------------------------------------------------------------------
        | LAST SLOT FIX (1-hour only)
        |--------------------------------------------------------------------------
        */
        if (startIndex === lastIndex) {

            $('#booking_start').append(`<option value="${selectedTime}">${selectedTime}</option>`);

            let nextHour = new Date(`1970-01-01 ${selectedTime}`);
            nextHour.setHours(nextHour.getHours() + 1);

            let endFormatted = nextHour.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });

            $('#booking_end').append(`<option value="${endFormatted}">${endFormatted}</option>`);

            $('#booking_start').val(selectedTime);
            $('#booking_end').val(endFormatted);

            $('#selected_date').val(selectedDate);
            $('#amount').val(Math.round(currentRate));

            $('#bookingModalService').modal('show');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD START DROPDOWN (skip blocked)
        |--------------------------------------------------------------------------
        */
        for (let i = 0; i < currentDaySlots.length; i++) {

            const time = currentDaySlots[i];

            if (blockedTimes[time]) continue;

            let hasValidEnd = false;

            for (let j = i + 1; j < currentDaySlots.length; j++) {

                const nextTime = currentDaySlots[j];

                // allow boundary
                if (blockedTimes[nextTime]) {
                    hasValidEnd = true;
                    break;
                }

                hasValidEnd = true;
                break;
            }

            if (!hasValidEnd) continue;

            $('#booking_start').append(`<option value="${time}">${time}</option>`);
        }

        $('#booking_start').val(selectedTime);
        $('#selected_date').val(selectedDate);

        updateEndTimeDropdown(selectedTime);
        calculateTotal();

        $('#bookingModalService').modal('show');
    });

    /*
    |--------------------------------------------------------------------------
    | START TIME CHANGE
    |--------------------------------------------------------------------------
    */
    $(document).on('change', '#bookingModalService #booking_start', function() {
        updateEndTimeDropdown($(this).val());
        calculateTotal();
    });

    /*
    |--------------------------------------------------------------------------
    | END TIME CHANGE
    |--------------------------------------------------------------------------
    */
    $(document).on('change', '#bookingModalService #booking_end', function() {
        calculateTotal();
    });

    /*
    |--------------------------------------------------------------------------
    | UPDATE END TIME (STOP AT BLOCKED)
    |--------------------------------------------------------------------------
    */
    function updateEndTimeDropdown(startTime) {

        const $end = $('#booking_end');
        $end.empty();

        const startIndex = currentDaySlots.indexOf(startTime);

        for (let i = startIndex + 1; i < currentDaySlots.length; i++) {

            const time = currentDaySlots[i];

            // always allow next slot
            $end.append(`<option value="${time}">${time}</option>`);

            // stop if blocked
            if (blockedTimes[time]) break;
        }

        if ($end.children().length === 0) {
            $end.append(`<option value="${startTime}">${startTime}</option>`);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATE TOTAL
    |--------------------------------------------------------------------------
    */
    function calculateTotal() {

        const startTime = $('#booking_start').val();
        const endTime   = $('#booking_end').val();

        if (!startTime || !endTime) return;

        const startIndex = currentDaySlots.indexOf(startTime);
        const endIndex   = currentDaySlots.indexOf(endTime);

        let hours = endIndex - startIndex;

        if (hours <= 0) hours = 1;

        const total = hours * currentRate;

        $('#amount').val(total.toFixed(2));
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT BOOKING
    |--------------------------------------------------------------------------
    */
    $('#confirm_booking_btn_service').on('click', function(e) {
        e.preventDefault();

        const payload = {
            action: 'handle_booking',
            type: 'service',
            nonce: bookingData.nonce,

            name: $('#booking_name').val(),
            email: $('#booking_email').val(),
            start: $('#booking_start').val(),
            end: $('#booking_end').val(),
            date: $('#selected_date').val(),
            comment: $('#booking_comment').val(),
            amount: $('#amount').val(),

            court_index: selectedCourt,
            service_id: currentServiceId
        };

        console.log('FINAL PAYLOAD:', payload);

        $.post(bookingData.ajaxurl, payload, function(res) {

            console.log('AJAX RESPONSE:', res);

            if (res.success && res.data && res.data.checkout_url) {

                window.open(res.data.checkout_url, '_blank');

                setTimeout(function(){
                    location.reload();
                }, 15000);

            } else {
                alert(res.data || 'Error submitting booking');
            }

        });
    });

});
})(jQuery);