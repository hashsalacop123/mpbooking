jQuery(function ($) {
    var calendarEl = document.getElementById('availability-calendar-frontend');
    if (!calendarEl) return;

    if (typeof availabilityData === 'undefined') {
        calendarEl.innerHTML = '<p class="mp-calendar-load-message">Availability settings could not be loaded. Refresh the page and try again.</p>';
        console.error('Coach availability calendar is missing its localized configuration.');
        return;
    }
    if (typeof FullCalendar === 'undefined') {
        calendarEl.innerHTML = '<p class="mp-calendar-load-message">The calendar could not load. Refresh the page or contact the site administrator.</p>';
        console.error('FullCalendar did not load on the coach profile page.');
        return;
    }

    var fieldKey = availabilityData.field_key || '';
    var calendar;

    function getTargetField() {
        if (!fieldKey) return $();
        var $field = $('#acf-' + fieldKey);
        if (!$field.length) $field = $('[name="acf[' + fieldKey + ']"]');
        if (!$field.length) $field = $('.acf-field[data-key="' + fieldKey + '"] textarea');
        return $field.first();
    }

    function readAvailabilityEvents() {
        var $field = getTargetField();
        if (!$field.length || !$field.val()) return [];
        try {
            var parsed = JSON.parse($field.val());
            return Array.isArray(parsed) ? parsed.filter(function (event) {
                return event && event.start && event.end;
            }) : [];
        } catch (error) {
            console.warn('Coach availability field is not valid JSON; starting with an empty calendar.');
            return [];
        }
    }

    function syncToACF() {
        if (!calendar) return;
        var availability = calendar.getEvents().filter(function (event) {
            return !event.extendedProps.isBooking;
        }).map(function (event) {
            return {
                start: event.start ? event.start.toISOString() : null,
                end: event.end ? event.end.toISOString() : null,
                title: event.title || 'Available',
                note: event.extendedProps.note || ''
            };
        }).filter(function (event) {
            return event.start && event.end;
        });

        var $field = getTargetField();
        if ($field.length) {
            $field.val(JSON.stringify(availability)).trigger('change');
        }
    }

    function dayAtSix(date) {
        var value = new Date(date);
        value.setHours(6, 0, 0, 0);
        return value;
    }

    function addSelection(startValue, endValue, note) {
        var start = new Date(startValue);
        var end = new Date(endValue);
        var cursor = new Date(start);
        var firstDay = true;

        while (cursor < end) {
            var segmentStart = firstDay ? new Date(cursor) : dayAtSix(cursor);
            var nextMidnight = new Date(cursor);
            nextMidnight.setHours(24, 0, 0, 0);
            var segmentEnd = end < nextMidnight ? new Date(end) : nextMidnight;

            if (segmentEnd > segmentStart) {
                calendar.addEvent({
                    start: segmentStart,
                    end: segmentEnd,
                    title: note || 'Available',
                    extendedProps: { note: note || '' }
                });
            }

            cursor = nextMidnight;
            firstDay = false;
        }
    }

    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        timeZone: 'local',
        firstDay: 1,
        height: 'auto',
        nowIndicator: true,
        allDaySlot: false,
        slotMinTime: '06:00:00',
        slotMaxTime: '24:00:00',
        slotDuration: '01:00:00',
        slotLabelInterval: '01:00',
        eventColor: '#4f9c61',
        validRange: { start: tomorrow },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        selectable: true,
        selectMirror: true,
        selectOverlap: false,
        editable: true,
        eventOverlap: false,
        eventAllow: function (dropInfo, draggedEvent) {
            return !draggedEvent.extendedProps.isBooking && dropInfo.start >= tomorrow;
        },
        events: readAvailabilityEvents().concat(availabilityData.booking_events || []),
        select: function (info) {
            var note = window.prompt('Add a short note for this availability? (Optional)', '');
            if (note !== null) {
                addSelection(info.start, info.end, note.trim());
                syncToACF();
            }
            calendar.unselect();
        },
        eventClick: function (info) {
            if (info.event.extendedProps.isBooking) {
                window.alert('Bookings are locked and cannot be removed from availability.');
                return;
            }
            if (window.confirm('Remove this availability time?')) {
                info.event.remove();
                syncToACF();
            }
        },
        eventDrop: syncToACF,
        eventResize: syncToACF,
        eventDidMount: function (info) {
            if (info.event.extendedProps.isBooking) {
                info.el.setAttribute('title', 'This time is reserved and cannot be changed.');
            }
        }
    });

    calendar.render();
    window.coachAvailabilityCalendar = calendar;

    $('#calendarModal').on('shown.bs.modal', function () {
        calendar.updateSize();
    });

    if (typeof acf !== 'undefined' && typeof acf.add_action === 'function') {
        acf.add_action('submit', function () {
            syncToACF();
        });
    }
    $('.mp-coach-dashboard form').on('submit', syncToACF);
});
