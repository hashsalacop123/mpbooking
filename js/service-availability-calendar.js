jQuery(function ($) {

    window.currentRow = null;

    /*
    |--------------------------------------------------------------------------
    | FIX: Prevent ACF row click when interacting with calendar
    |--------------------------------------------------------------------------
    */
    $(document).on('mousedown click', '#availability-calendar-frontend, #availability-calendar-frontend *', function(e) {
        e.stopPropagation();
    });

    /*
    |--------------------------------------------------------------------------
    | Add button (SAFE)
    |--------------------------------------------------------------------------
    */
    function addButtons() {
        $('.acf-row').each(function () {

            if ($(this).find('.open-calendar').length) return;

            $(this).append(
                '<div class="calendar-btn-cell"><button type="button" class="btn btn-primary open-calendar">Calendar</button></div>'
            );

        });
    }

    $(window).on('load', function () {
        addButtons();
        setTimeout(addButtons, 500);
    });

    /*
    |--------------------------------------------------------------------------
    | CLICK → OPEN MODAL ONLY
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.open-calendar', function () {

        window.currentRow = $(this).closest('.acf-row');
        $('#calendarModal').modal('show');

    });

    /*
    |--------------------------------------------------------------------------
    | MODAL → INIT + LOAD + RENDER
    |--------------------------------------------------------------------------
    */
    $('#calendarModal').on('shown.bs.modal', function () {

        setTimeout(function () {

            var calendarEl = document.getElementById('availability-calendar-frontend');
            if (!calendarEl) return;

            if (typeof calendar === 'undefined') {

                console.log('Init calendar');

                window.calendar = new FullCalendar.Calendar(calendarEl, {

                    validRange: function() {
                        let now = new Date();
                        now.setDate(now.getDate() + 1);
                        now.setHours(0, 0, 0, 0);
                        return { start: now };
                    },

                    initialView: 'timeGridWeek',

                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },

                    selectable: true,
                    editable: true,
                    allDaySlot: false,
                    timeZone: 'local',

                    // ✅ LIMIT UI TIME RANGE
                    slotMinTime: "06:00:00",
                    slotMaxTime: "24:00:00",
                    slotDuration: '01:00:00',

                    /*
                    |--------------------------------------------------------------------------
                    | HARD LIMIT SELECTION (6AM–12AM)
                    |--------------------------------------------------------------------------
                    */
                selectAllow: function(selectInfo) {

                let startHour = selectInfo.start.getHours();

                if (startHour < 6) return false;

                return true;
            },

                    /*
                    |--------------------------------------------------------------------------
                    | SELECT EVENT
                    |--------------------------------------------------------------------------
                    */
                    select: function(info) {

                        var note = prompt('Note (Optional):');

                        let start = new Date(info.start);
                        let end   = new Date(info.end);

                        let current = new Date(start);

                        while (current < end) {

                            let dayStart = new Date(current);
                            let dayEnd   = new Date(current);

                            // ✅ enforce min 6AM
                            if (dayStart.getHours() < 6) {
                                dayStart.setHours(6, 0, 0, 0);
                            }

                            // ✅ enforce max midnight
                            dayEnd.setHours(24, 59, 59, 0);
                            if (dayEnd > end) {
                                dayEnd = new Date(end);
                            }

                            calendar.addEvent({
                                start: new Date(dayStart),
                                end: new Date(dayEnd),
                                title: note ? note : 'Available',
                                extendedProps: { note: note || '' }
                            });

                        current.setDate(current.getDate() + 1);

                        // only force 6AM if needed
                            if (current.getHours() < 6) {
                                current.setHours(6, 0, 0, 0);
                            }
                        }

                        calendar.unselect();

                        if (typeof syncToACF === 'function') {
                            syncToACF();
                        }
                    },

                    eventClick: function(info) {
                        if(confirm('Remove this slot?')) {
                            info.event.remove();
                        }
                    }
                });

                calendar.render();
            }

            /*
            |--------------------------------------------------------------------------
            | LOAD EVENTS FROM CURRENT ROW
            |--------------------------------------------------------------------------
            */
            if (window.currentRow) {

                let $field = window.currentRow.find('.acf-field[data-name="court_calendar"] textarea');
                let events = [];

                if ($field.length && $field.val()) {
                    try {
                        events = JSON.parse($field.val());
                    } catch(e) {}
                }

                calendar.removeAllEvents();

                events.forEach(function(e){
                    calendar.addEvent(e);
                });

                console.log('Loaded events:', events);
            }

            calendar.updateSize();

        }, 200);

    });

    /*
    |--------------------------------------------------------------------------
    | SAVE BUTTON
    |--------------------------------------------------------------------------
    */
    $('#saveCalendar').on('click', function () {

        if (!window.currentRow || typeof calendar === 'undefined') return;

        let events = calendar.getEvents().map(function(event) {
            return {
                start: event.start.toISOString(),
                end: event.end.toISOString(),
                title: event.title || 'Available',
                note: event.extendedProps.note || ''
            };
        });

        let $field = window.currentRow.find('.acf-field[data-name="court_calendar"] textarea');

        if ($field.length) {
            $field.val(JSON.stringify(events)).trigger('change');
        }

        $('#calendarModal').modal('hide');

    });

    /*
    |--------------------------------------------------------------------------
    | ACF SUBMIT
    |--------------------------------------------------------------------------
    */
    if (typeof acf !== 'undefined') {

        acf.add_action('submit', function () {

            console.log('ACF SUBMIT — sync all');

            $('.acf-row').each(function () {

                let $row = $(this);
                let $field = $row.find('.acf-field[data-name="court_calendar"] textarea');

                if (!$field.length) return;

                if ($row.is(window.currentRow) && typeof calendar !== 'undefined') {

                    let events = calendar.getEvents().map(function(event) {
                        return {
                            start: event.start.toISOString(),
                            end: event.end.toISOString(),
                            title: event.title || 'Available',
                            note: event.extendedProps.note || ''
                        };
                    });

                    $field.val(JSON.stringify(events)).trigger('change');
                }

            });

        });

    }

});