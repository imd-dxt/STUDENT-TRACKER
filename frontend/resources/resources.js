document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const resourceList = document.getElementById('resourceList');

    fetchResources();

    uploadForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(uploadForm);

        fetch('http://localhost/STUDENT-TRACKER/backend/resources/add_resource.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            fetchResources();
            uploadForm.reset();
        })
        .catch(error => console.error('Error:', error));
    });

    function fetchResources() {
        fetch('http://localhost/STUDENT-TRACKER/backend/resources/get_resource.php')
        .then(response => response.json())
        .then(data => {
            resourceList.innerHTML = '';
            data.forEach(resource => {
                const listItem = document.createElement('li');
                listItem.className = 'resource-item';
                listItem.innerHTML = `
                    <div class="resource-info">
                        <i class="fas fa-file-pdf resource-icon"></i>
                        <div>
                            <span class="resource-title">${resource.title}</span>
                            <span class="resource-subject">${resource.subject}</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <a href="http://localhost/STUDENT-TRACKER/backend/resources/${resource.file_path}" target="_blank" class="btn btn-secondary btn-sm">View</a>
                        <button data-id="${resource.id}" class="btn btn-danger btn-sm deleteBtn">Delete</button>
                    </div>
                `;
                resourceList.appendChild(listItem);
            });

            document.querySelectorAll('.deleteBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const resourceId = this.getAttribute('data-id');
                    deleteResource(resourceId);
                });
            });
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteResource(resourceId) {
        const formData = new FormData();
        formData.append('resource_id', resourceId);

        fetch('http://localhost/STUDENT-TRACKER/backend/resources/delete_resource.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            fetchResources();
        })
        .catch(error => console.error('Error:', error));
    }
});

