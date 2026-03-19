jQuery(document).ready(function($) {
    if (typeof FullCalendar === 'undefined') return;
    if (typeof availabilityData === 'undefined') return;

    var calendarEl = document.getElementById('availability-calendar-frontend');
    if (!calendarEl) return;

    // Use localized data

var fieldKey = availabilityData.field_key;
    
    // ACF form selector: tries ID first, then the 'name' attribute which ACF always uses
    function getTargetField() {
        var $byID = $('#acf-field_' + fieldKey);
        if ($byID.length) return $byID;
        return $('[name="acf[' + fieldKey + ']"]');
    }

    var $field = getTargetField();

    // Load Existing Data
    var existingEvents = [];
    if ($field.length && $field.val()) {
        try {
            existingEvents = JSON.parse($field.val());
        } catch(e) { console.warn("New profile or invalid JSON."); }
    }

    // Load booking events from PHP
var bookingEvents = availabilityData.booking_events || [];


    // Sync Function
    function syncToACF() {
        var events = calendar.getEvents().map(function(event) {
            return {
                start: event.start.toISOString(),
                end: event.end.toISOString(),
                title: event.title || 'Available',
                note: event.extendedProps.note || ''
            };
        });
        
        var jsonValue = JSON.stringify(events);
        var $currentField = getTargetField(); // re-fetch in case DOM changed
        if ($currentField.length) {
            $currentField.val(jsonValue);
            console.log("Synced to " + fieldKey, jsonValue);
        }
    }

    // Initialize Calendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        // 1. The "Zoom In" Logic
    dateClick: function(info) {
        // If the user clicks a day in Month view, jump to that Day's hourly view
        if (calendar.view.type === 'dayGridMonth') {
            calendar.changeView('timeGridDay', info.dateStr);
        }
    },
        
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        navLinks: true, // Also makes the small date numbers clickable
        selectable: true,
        editable: true,
        selectable: true,
        editable: true,
        allDaySlot: false,
        timeZone: 'local', // Add this line
        slotMinTime: "06:00:00",
        slotMaxTime: "22:00:00",
        slotDuration: '01:00:00', // <-- 1 hour slots

events: existingEvents.concat(bookingEvents),
 select: function(info) {

    var note = prompt('Note (Optional):');

    calendar.addEvent({
        start: info.start,
        end: info.end,
        title: note ? note : 'Available',
        extendedProps: { note: note || '' }
    });

    calendar.unselect();
    syncToACF();
},
        eventClick: function(info) {
            if(confirm('Remove this slot?')) {
                info.event.remove();
                syncToACF();
            }
        },
        eventDrop: syncToACF,
        eventResize: syncToACF
    });

    // Modal Display Fix
    $('#calendarModal').on('shown.bs.modal', function () {
        calendar.render();
        calendar.updateSize();
    });

    // ACF-Specific Submit Hook (Essential for acf_form)
    if (typeof acf !== 'undefined') {
        acf.add_action('submit', function($form) {
            syncToACF();
        });
    }
});
// console.log("Calendar data:", availabilityData);