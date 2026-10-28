(function ($) {
    'use strict';

    $(function () {
        const dataNode = document.getElementById('mp-court-calendar-data');
        const grid = document.querySelector('.mp-court-month-grid');
        const tableBody = document.querySelector('.mp-court-table-body');
        if (!dataNode || !grid || !tableBody) return;

        let calendarData;
        try {
            calendarData = JSON.parse(dataNode.textContent || '{}');
        } catch (error) {
            grid.textContent = 'Court availability could not be loaded.';
            return;
        }

        const courts = calendarData.courts || {};
        const days = calendarData.days || {};
        const dayKeys = Object.keys(days).sort();
        const todayParts = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit' }).formatToParts(new Date());
        const todayPart = (type) => todayParts.find((part) => part.type === type).value;
        const today = `${todayPart('year')}-${todayPart('month')}-${todayPart('day')}`;
        let selectedDate = dayKeys.find((date) => date >= today && hasOpenSlots(date)) || dayKeys.find((date) => date >= today) || dayKeys[0] || '';
        let visibleMonth = selectedDate ? selectedDate.slice(0, 7) : today.slice(0, 7);
        let activeBooking = null;

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const dateParts = (date) => date.split('-').map(Number);
        const monthKey = (year, month) => `${year}-${String(month + 1).padStart(2, '0')}`;
        const dateKey = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

        function formatLongDate(key) {
            const [year, month, day] = dateParts(key);
            return new Intl.DateTimeFormat('en-US', { timeZone: 'UTC', weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }).format(new Date(Date.UTC(year, month - 1, day)));
        }

        function formatTime(timestamp) {
            return new Intl.DateTimeFormat('en-US', { timeZone: 'Asia/Manila', hour: 'numeric', minute: '2-digit', hour12: true }).format(new Date(timestamp * 1000)).toUpperCase();
        }

        function minuteOfDay(label) {
            const match = label.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
            if (!match) return 0;
            let hour = Number(match[1]) % 12;
            if (match[3].toUpperCase() === 'PM') hour += 12;
            return hour * 60 + Number(match[2]);
        }

        function hasSlotsOn(date) {
            return Boolean(days[date] && days[date].slots && Object.keys(days[date].slots).length);
        }

        function hasOpenSlots(date) {
            const courtSlots = (days[date] && days[date].slots) || {};
            return Object.values(courtSlots).some((slots) => Object.values(slots).some((slot) => slot.status === 'available'));
        }

        function selectDate(date) {
            if (!hasSlotsOn(date)) return;
            selectedDate = date;
            visibleMonth = date.slice(0, 7);
            renderCalendar();
            renderAvailability();
        }

        function renderCalendar() {
            const [year, month] = visibleMonth.split('-').map(Number);
            const monthIndex = month - 1;
            document.querySelector('.mp-court-month-label').textContent = `${monthNames[monthIndex]} ${year}`;

            const firstWeekday = new Date(year, monthIndex, 1).getDay();
            const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
            const cells = [];
            for (let blank = 0; blank < firstWeekday; blank++) {
                const spacer = document.createElement('span');
                spacer.className = 'mp-court-calendar-spacer';
                spacer.setAttribute('role', 'presentation');
                cells.push(spacer);
            }
            for (let day = 1; day <= daysInMonth; day++) {
                const key = `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const button = document.createElement('button');
                const available = hasSlotsOn(key);
                button.type = 'button';
                button.className = 'mp-court-date' + (available ? ' has-slots' : ' is-disabled') + (hasOpenSlots(key) ? ' has-open-slots' : ' is-full') + (selectedDate === key ? ' is-selected' : '');
                button.textContent = String(day);
                button.disabled = !available;
                button.setAttribute('role', 'gridcell');
                button.setAttribute('aria-label', `${formatLongDate(key)}${hasOpenSlots(key) ? ', availability' : available ? ', no available times' : ', unavailable'}`);
                button.setAttribute('aria-selected', selectedDate === key ? 'true' : 'false');
                button.addEventListener('click', () => selectDate(key));
                cells.push(button);
            }
            grid.replaceChildren(...cells);
        }

        function renderAvailability() {
            const dateText = document.querySelector('.mp-court-selected-date');
            const emptyMessage = document.querySelector('.mp-court-empty-availability');
            const tableWrap = document.querySelector('.mp-court-availability-table-wrap');
            const selectedSlots = (days[selectedDate] && days[selectedDate].slots) || {};
            const courtIndexes = Object.keys(courts).sort((a, b) => Number(a) - Number(b));

            dateText.textContent = selectedDate ? formatLongDate(selectedDate) : 'Select a highlighted date';
            tableBody.replaceChildren();
            document.querySelector('.mp-court-table-date th').colSpan = Math.max(2, courtIndexes.length + 1);

            const allTimes = new Set();
            Object.values(selectedSlots).forEach((slots) => Object.keys(slots).forEach((time) => allTimes.add(time)));
            const orderedTimes = Array.from(allTimes).sort((a, b) => minuteOfDay(a) - minuteOfDay(b));

            orderedTimes.forEach((time) => {
                const row = document.createElement('tr');
                const timeCell = document.createElement('th');
                timeCell.scope = 'row';
                timeCell.className = 'mp-court-time-cell';
                const courtSlotsForTime = Object.values(selectedSlots).map((slots) => slots[time]).filter(Boolean);
                const startTimestamp = courtSlotsForTime.length ? courtSlotsForTime[0].start : 0;
                timeCell.textContent = `${time} – ${formatTime(startTimestamp + 3600)}`;
                row.appendChild(timeCell);

                courtIndexes.forEach((index) => {
                    const cell = document.createElement('td');
                    const slot = selectedSlots[index] && selectedSlots[index][time];
                    if (!slot) {
                        const unavailable = document.createElement('span');
                        unavailable.className = 'mp-court-slot-state unavailable';
                        unavailable.textContent = '—';
                        unavailable.setAttribute('aria-label', 'Unavailable');
                        cell.appendChild(unavailable);
                    } else if (slot.status === 'available') {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'mp-court-slot-button available';
                        button.textContent = 'Available';
                        button.dataset.date = selectedDate;
                        button.dataset.court = index;
                        button.dataset.time = time;
                        button.addEventListener('click', () => openBooking(selectedDate, index, time));
                        cell.appendChild(button);
                    } else {
                        const state = document.createElement('span');
                        state.className = `mp-court-slot-state ${slot.status}`;
                        state.textContent = slot.status === 'pending' ? 'Pending' : 'Booked';
                        cell.appendChild(state);
                    }
                    row.appendChild(cell);
                });
                tableBody.appendChild(row);
            });

            const hasTimes = orderedTimes.length > 0;
            emptyMessage.hidden = hasTimes;
            tableWrap.hidden = !hasTimes;
        }

        function openBooking(date, courtIndex, time) {
            const courtSlots = ((days[date] || {}).slots || {})[courtIndex] || {};
            const selectedSlot = courtSlots[time];
            if (!selectedSlot || selectedSlot.status !== 'available') return;

            const times = Object.keys(courtSlots).sort((a, b) => minuteOfDay(a) - minuteOfDay(b));
            const startIndex = times.indexOf(time);
            const endSelect = document.getElementById('booking_end');
            const startSelect = document.getElementById('booking_start');
            const contiguousSlots = [];
            let expectedStart = selectedSlot.start;

            for (let i = startIndex; i < times.length; i++) {
                const slot = courtSlots[times[i]];
                if (!slot || slot.status !== 'available' || slot.start !== expectedStart) break;
                contiguousSlots.push(slot);
                expectedStart = slot.end;
            }
            if (!contiguousSlots.length) return;

            startSelect.replaceChildren();
            const startOption = document.createElement('option');
            startOption.value = time;
            startOption.textContent = time;
            startSelect.appendChild(startOption);

            endSelect.replaceChildren();
            contiguousSlots.forEach((slot) => {
                const option = document.createElement('option');
                option.value = formatTime(slot.end);
                option.textContent = formatTime(slot.end);
                option.dataset.endTimestamp = String(slot.end);
                endSelect.appendChild(option);
            });

            const rate = Number(courts[courtIndex] && courts[courtIndex].rate) || 0;
            activeBooking = { date, courtIndex: Number(courtIndex), start: time, startTimestamp: selectedSlot.start, rate };
            document.getElementById('selected_date').value = date;
            document.getElementById('selected_court').value = String(courtIndex);
            document.querySelector('.mp-court-modal-summary').textContent = `${courts[courtIndex].name} · ${formatLongDate(date)}`;
            updateBookingSummary();

            if ($.fn.modal) $('#bookingModalService').modal('show');
            else document.getElementById('bookingModalService').classList.add('show');
        }

        function updateBookingSummary() {
            if (!activeBooking) return;
            const endOption = document.getElementById('booking_end').selectedOptions[0];
            if (!endOption) return;
            const endTimestamp = Number(endOption.dataset.endTimestamp);
            const hours = Math.max(1, Math.round((endTimestamp - activeBooking.startTimestamp) / 3600));
            const base = hours * activeBooking.rate;
            const paymongoRate = Number(document.getElementById('paymongo_fee').value) || 0;
            const bookingFeeRate = Number(document.getElementById('web_admin_fee').value) || 0;
            const paymongoFee = base * paymongoRate / 100;
            const bookingFee = base * bookingFeeRate / 100;
            const total = base + paymongoFee + bookingFee;

            document.getElementById('amount').value = total.toFixed(2);
            const summary = document.querySelector('#booking_summary .summary-content');
            summary.textContent = `Date: ${formatLongDate(activeBooking.date)} · Time: ${activeBooking.start} – ${endOption.value} · ${hours} hour(s) · Total: ₱${total.toFixed(2)}`;
            document.getElementById('booking_summary').hidden = false;
        }

        function shiftMonth(offset) {
            const [year, month] = visibleMonth.split('-').map(Number);
            const next = new Date(year, month - 1 + offset, 1);
            visibleMonth = monthKey(next.getFullYear(), next.getMonth());
            renderCalendar();
        }
        document.querySelector('.mp-court-month-prev').addEventListener('click', () => shiftMonth(-1));
        document.querySelector('.mp-court-month-next').addEventListener('click', () => shiftMonth(1));
        document.querySelector('.mp-court-refresh').addEventListener('click', () => window.location.reload());
        $('#bookingModalService #booking_end').on('change', updateBookingSummary);

        $('#confirm_booking_btn_service').on('click', function (event) {
            event.preventDefault();
            if (!activeBooking) return;

            const name = $('#booking_name').val().trim();
            const email = $('#booking_email').val().trim();
            if (!name || !email) {
                window.alert('Please enter your name and email to continue.');
                return;
            }

            const $button = $(this).prop('disabled', true).text('Creating booking…');
            $.post(window.bookingData.ajaxurl, {
                action: 'handle_booking',
                type: 'service',
                nonce: window.bookingData.nonce,
                name,
                email,
                start: activeBooking.start,
                end: $('#booking_end').val(),
                date: activeBooking.date,
                comment: $('#booking_comment').val(),
                amount: $('#amount').val(),
                court_index: activeBooking.courtIndex,
                service_id: calendarData.serviceId
            }).done(function (response) {
                if (response && response.success && response.data && response.data.checkout_url) {
                    window.location.assign(response.data.checkout_url);
                    return;
                }
                window.alert((response && response.data) || 'This court time is no longer available. Please refresh the calendar and choose another time.');
                window.location.reload();
            }).fail(function (xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.data;
                window.alert(message || 'We could not create your booking. Please try again.');
                $button.prop('disabled', false).text('Continue to payment');
            });
        });

        if (selectedDate) renderCalendar();
        else {
            document.querySelector('.mp-court-month-label').textContent = monthNames[Number(visibleMonth.slice(5, 7)) - 1] + ' ' + visibleMonth.slice(0, 4);
            grid.innerHTML = '<p class="mp-court-no-dates">No upcoming court dates have been posted.</p>';
        }
        renderAvailability();
    });
})(jQuery);
