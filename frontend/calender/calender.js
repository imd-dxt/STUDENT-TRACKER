document.addEventListener('DOMContentLoaded', function() {
    const eventForm = document.getElementById('eventForm');
    const eventList = document.getElementById('eventList');
    let calendar;

    initializeCalendar();
    fetchEvents();

    eventForm.addEventListener('submit', function(e) {
        e.preventDefault();
        addEvent();
    });

    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: fetchCalendarEvents,
            eventClick: function(info) {
                alert('Event: ' + info.event.title + '\nDescription: ' + info.event.extendedProps.description);
            }
        });
        calendar.render();
    }

    function fetchCalendarEvents(fetchInfo, successCallback, failureCallback) {
        fetch('/STUDENT-TRACKER/backend/calender/get_events.php', {
            method: 'GET'
        })
        .then(response => response.json())
        .then(events => {
            if (events.success === false) {
                failureCallback('Error fetching events: ' + events.error);
                return;
            }
            const calendarEvents = events.map(event => ({
                id: event.id,
                title: event.title,
                start: event.event_date,
                description: event.description
            }));
            successCallback(calendarEvents);
        })
        .catch(error => {
            failureCallback('Error fetching events: ' + error);
        });
    }

    function fetchEvents() {
        fetch('/STUDENT-TRACKER/backend/calender/get_events.php', {
            method: 'GET'
        })
        .then(response => response.json())
        .then(events => {
            if (events.success === false) {
                alert('Error fetching events: ' + events.error);
                return;
            }
            eventList.innerHTML = '';
            events.forEach(event => {
                const eventItem = document.createElement('li');
                eventItem.className = 'event-item';
                eventItem.innerHTML = `
                    <div class="event-info">
                        <div class="event-title">${event.title}</div>
                        <div class="event-date">${formatDate(event.event_date)}</div>
                        <div class="event-description">${event.description}</div>
                    </div>
                    <button class="delete-btn" data-id="${event.id}">Delete</button>
                `;
                eventList.appendChild(eventItem);
            });
            addDeleteEventListeners();
        });
    }

    function addEvent() {
        const formData = new FormData(eventForm);
        fetch('/STUDENT-TRACKER/backend/calender/add_event.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                fetchEvents();
                calendar.refetchEvents();
                eventForm.reset();
            } else {
                alert('Error adding event: ' + result.error);
            }
        });
    }

    function deleteEvent(eventId) {
        fetch(`/STUDENT-TRACKER/backend/calender/delete_event.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: eventId })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                fetchEvents();
                calendar.refetchEvents();
            } else {
                alert('Error deleting event: ' + result.error);
            }
        });
    }

    function addDeleteEventListeners() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-id');
                deleteEvent(eventId);
            });
        });
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }
});

