(function($) {
"use strict";

$(document).ready(function() {

    let currentDaySlots = [];
    let blockedTimes = {};

    /*
    |--------------------------------------------------------------------------
    | CLICK SLOT
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.availability-coach', function() {

        const $day = $(this).closest('.day-parent');

        blockedTimes = {};
        $day.find('.availability-coach.pending, .availability-coach.booked').each(function() {
            blockedTimes[$(this).data('time')] = true;
        });

        const selectedTime = $(this).data('time');
        const selectedDate = $(this).data('date');

        const rawSlots = $day.find('.day-slots').val();
        if (!rawSlots) return;

        currentDaySlots = JSON.parse(rawSlots);

        const rate = parseFloat($(this).closest('.slider-calendar').data('rate')) || 0;
        $('#bookingModal').data('rate', rate);

        const startIndex = currentDaySlots.indexOf(selectedTime);
        const lastIndex  = currentDaySlots.length - 1;

        $('#booking_start').empty();
        $('#booking_end').empty();

        if (blockedTimes[selectedTime]) return;

        /*
        |--------------------------------------------------------------------------
        | LAST SLOT
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
            $('#amount').val(Math.round(rate));

            $('#bookingModal').modal('show');

            setTimeout(function() {
                updateBookingSummaryRealtime();
            }, 100);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD START DROPDOWN
        |--------------------------------------------------------------------------
        */
        for (let i = 0; i < currentDaySlots.length; i++) {

            const time = currentDaySlots[i];

            if (blockedTimes[time]) continue;

            let hasValidEnd = false;

            for (let j = i + 1; j < currentDaySlots.length; j++) {
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

        $('#bookingModal').modal('show');

        setTimeout(function() {
            updateBookingSummaryRealtime();
        }, 100);
    });

    /*
    |--------------------------------------------------------------------------
    | START CHANGE
    |--------------------------------------------------------------------------
    */
    $(document).on('change', '#booking_start', function() {
        updateEndTimeDropdown($(this).val());
        calculateTotal();
        updateBookingSummaryRealtime();
    });

    /*
    |--------------------------------------------------------------------------
    | END CHANGE
    |--------------------------------------------------------------------------
    */
    $(document).on('change', '#booking_end', function() {
        calculateTotal();
        updateBookingSummaryRealtime();
    });

    /*
    |--------------------------------------------------------------------------
    | UPDATE END TIME
    |--------------------------------------------------------------------------
    */
    function updateEndTimeDropdown(startTime) {

        const $end = $('#booking_end');
        $end.empty();

        const startIndex = currentDaySlots.indexOf(startTime);

        for (let i = startIndex + 1; i < currentDaySlots.length; i++) {

            const time = currentDaySlots[i];

            if (blockedTimes[time]) break;

            $end.append(`<option value="${time}">${time}</option>`);
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

    const rate = parseFloat($('#bookingModal').data('rate')) || 0;

    const baseTotal = hours * rate;

    // Get percentage values
    const paymongoFee = parseFloat($('#paymongo_fee').val()) || 0;
    const webAdminFee = parseFloat($('#web_admin_fee').val()) || 0;

    // Compute percentage amounts
    const paymongoAmount = (paymongoFee / 100) * baseTotal;
    const webAdminAmount = (webAdminFee / 100) * baseTotal;

    const total = baseTotal + paymongoAmount + webAdminAmount;

    // keep your rounding behavior
    $('#amount').val(Math.round(total));
}

    /*
    |--------------------------------------------------------------------------
    | REAL-TIME SUMMARY
    |--------------------------------------------------------------------------
    */
function updateBookingSummaryRealtime() {

    const start = $('#booking_start').val();
    const end   = $('#booking_end').val();
    const date  = $('#selected_date').val();

    if (!start || !end || !date) {
        $('#booking_summary').hide();
        return;
    }

    const rate = parseFloat($('#bookingModal').data('rate')) || 0;

    const startIndex = currentDaySlots.indexOf(start);
    const endIndex   = currentDaySlots.indexOf(end);

    let hours = endIndex - startIndex;
    if (hours <= 0) hours = 1;

    const baseTotal = hours * rate;

    // Get percentage values
    const paymongoFee = parseFloat($('#paymongo_fee').val()) || 0;
    const webAdminFee = parseFloat($('#web_admin_fee').val()) || 0;

    // Compute percentage amounts
    const paymongoAmount = (paymongoFee / 100) * baseTotal;
    const webAdminAmount = (webAdminFee / 100) * baseTotal;

    const total = baseTotal + paymongoAmount + webAdminAmount;

    const html = `
        <div>Date: ${date}</div>
        <div>Time: ${start} - ${end}</div>
        <div>Duration: ${hours} hour(s)</div>
        <div>Base: ₱${Math.round(baseTotal)}</div>
        <div>PayMongo Fee <span class = "perce">(${paymongoFee}%)</span>: ₱${Math.round(paymongoAmount)}</div>
        <div>Web Fee <span class = "perce">(${webAdminFee}%)</span>: ₱${Math.round(webAdminAmount)}</div>
        <div><strong>Total: ₱${Math.round(total)}</strong></div>
    `;

    $('#booking_summary').show();
    $('#booking_summary .summary-content').html(html);
}

    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */
$('#confirm_booking_btn_coach').on('click', function(e) {
    e.preventDefault();

    const payload = {
        action: 'handle_booking',
        type: 'coach',
        nonce: bookingData.nonce,

        name: $('#booking_name').val(),
        email: $('#booking_email').val(),
        start: $('#booking_start').val(),
        end: $('#booking_end').val(),
        date: $('#selected_date').val(),
        comment: $('#booking_comment').val(),
        amount: $('#amount').val(),

        coach_id: bookingData.coach_id
    };

    console.log('FINAL PAYLOAD:', payload);

    $.post(bookingData.ajaxurl, payload, function(res) {

        console.log('AJAX RESPONSE:', res);

        if (res.success && res.data && res.data.checkout_url) {

            // ✅ open PayMongo
            window.open(res.data.checkout_url, '_blank');

            // ✅ SweetAlert hybrid (auto + manual)
            let timerInterval;

            Swal.fire({
                title: 'Waiting for Payment',
                html: 'Complete your payment in the new tab.<br><br>Auto refresh in <b>15</b> seconds...',
                timer: 15000,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonText: 'I already paid',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                    const b = Swal.getHtmlContainer().querySelector('b');
                    timerInterval = setInterval(() => {
                        if (b) {
                            b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                        }
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                    location.reload();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: res.data || 'Error submitting booking'
            });

        }

    });
});

});
})(jQuery);