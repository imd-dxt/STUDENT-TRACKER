document.addEventListener('DOMContentLoaded', function() {
    const eventForm = document.getElementById('eventForm');
    const eventList = document.getElementById('eventList');

    
    fetchEvents();

  
    eventForm.addEventListener('submit', function(e) {
        e.preventDefault();
        addEvent();
    });

   
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
                eventItem.textContent = `${event.title} - ${event.description} (${event.event_date})`;
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.addEventListener('click', () => deleteEvent(event.id));
                eventItem.appendChild(deleteButton);
                eventList.appendChild(eventItem);
            });
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
            } else {
                alert('Error deleting event: ' + result.error);
            }
        });
    }
});