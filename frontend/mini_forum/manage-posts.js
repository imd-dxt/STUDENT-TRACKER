document.addEventListener('DOMContentLoaded', function() {
    fetchPosts();

    document.getElementById('post-content').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            createPost();
        }
    });
});

function fetchPosts() {
    fetch('http://localhost/STUDENT-TRACKER/backend/mini_forum/get_user_post.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('posts-container');
            container.innerHTML = '';
            data.forEach(post => {
                const postElement = document.createElement('div');
                postElement.innerHTML = `
                    <h3>${post.name}</h3>
                    <p>${post.content}</p>
                    <small>${new Date(post.created_at).toLocaleString()}</small>
                    
                    <button onclick="deletePost(${post.id})">Delete</button>
                `;
                container.appendChild(postElement);
            });
        })
        .catch(error => console.error('Error fetching posts:', error));
}

function createPost() {
    console .log('Creating post');
    const content = document.getElementById('post-content').value.trim();
    if (!content) {
        alert('Content cannot be empty');
        return;
    }

    fetch('http://localhost/STUDENT-TRACKER/backend/mini_forum/create_post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ content }),
        credentials: 'include'
    })
    .then(response => response.text())
    .then(text => {
        try {
            const jsonResponse = JSON.parse(text);
            console.log('Post creation response:', jsonResponse); 
            if (jsonResponse.success) {
                fetchPosts(); 
            } else {
                console.error('Error creating post:', jsonResponse.message);
            }
        } catch (error) {
            console.error('Error parsing JSON response:', error, text);
        }
    })
    .catch(error => console.error('Error creating post:', error));
}

function deletePost(postId) {
    fetch('http://localhost/STUDENT-TRACKER/backend/mini_forum/delete_post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId }),
        credentials: 'include'
    })
    .then(response => response.text()) // Get the response as text
    .then(text => {
        try {
            const data = JSON.parse(text); // Try to parse the text as JSON
            if (data.success) {
                fetchPosts();
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error parsing JSON:', error);
            console.error('Response text:', text); // Log the response text for debugging
        }
    })
    .catch(error => console.error('Error deleting post:', error));
}
