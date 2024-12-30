document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get('id');
    console.log('Post ID:', postId);
    fetchPost(postId);
    fetchComments(postId);

    document.getElementById('comment-content').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            createComment(postId);
        }
    });
});

function fetchPost(postId) {
    fetch(`http://localhost/STUDENT-TRACKER/backend/mini_forum/get_single_post.php?id=${postId}`)
        .then(response => response.json())
        .then(post => {
            console.log('Post data:', post);
            const container = document.getElementById('post-container');
            if (post) {
                container.innerHTML = `
                    
                    <h3><i class="fas fa-user-circle"></i> ${post.name}</h3>
                    <p><i class="fas fa-quote-left"></i> ${post.content}</p>
                    <small><i class="far fa-clock"></i> ${new Date(post.created_at).toLocaleString()}</small>
                `;
            } else {
                container.innerHTML = '<p>Post not found.</p>';
            }
        })
        .catch(error => console.error('Error fetching post:', error));
}

function fetchComments(postId) {
    fetch(`http://localhost/STUDENT-TRACKER/backend/mini_forum/get_comments.php?post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('comments-container');
            container.innerHTML = '';
            getLoggedInStudentId().then(currentStudentId => {
                console.log('Current student ID:', currentStudentId);
                data.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.classList.add('comment');
                    commentElement.innerHTML = `
                        <h4>${comment.name}</h4>
                        <p>${comment.content}</p>
                        <small>${new Date(comment.created_at).toLocaleString()}</small>
                        ${comment.student_id === currentStudentId ? `<button class="cta-button" onclick="deleteComment(${comment.id})"><i class="fas fa-trash-alt"></i>  Delete</button>` : ''}
                    `;
                    container.appendChild(commentElement);
                });
            });
        })
        .catch(error => console.error('Error fetching comments:', error));
}

function createComment(postId) {
    const content = document.getElementById('comment-content').value.trim();
    if (!content) {
        alert('Content cannot be empty');
        return;
    }

    fetch('http://localhost/STUDENT-TRACKER/backend/mini_forum/create_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId, content }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('comment-content').value = '';
            fetchComments(postId);
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error creating comment:', error));
    console.log('Comment created');
}

function deleteComment(commentId) {
    fetch('http://localhost/STUDENT-TRACKER/backend/mini_forum/delete_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ comment_id: commentId }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const urlParams = new URLSearchParams(window.location.search);
            const postId = urlParams.get('id');
            fetchComments(postId);
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error deleting comment:', error));

}
function getLoggedInStudentId() {
    return fetch('http://localhost/STUDENT-TRACKER/backend/mini_forum/get_user_info.php', {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => { // Debugging line
        return data.student_id;
    })
    .catch(error => {
        console.error('Error fetching student ID:', error);
        return null;
    });
}