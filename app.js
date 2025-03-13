document.addEventListener('DOMContentLoaded', () => {
  const articleList = document.getElementById('article-list');

  // Hiển thị danh sách bài viết
  fetch('fetch.php')
  .then(response => response.json())
  .then(data => {
      let savedPostsContainer = document.getElementById('savedPosts');
      savedPostsContainer.innerHTML = '';

      data.forEach(post => {
          let postElement = `
              <div class="post">
                  <h3>Người đăng: ${post.username}</h3>
                  <p>Nội dung: ${post.content}</p>
                  <small>Saved at: ${post.saved_at}</small>
                  <button onclick="deletePost(${post.saved_id})">Xóa</button>
              </div>
          `;
          savedPostsContainer.innerHTML += postElement;
      });
  })
  .catch(error => console.error('Lỗi:', error));


  // Xóa bài viết
  window.deleteArticle = function (id) {
      if (confirm('Bạn có chắc muốn xóa bài viết này không?')) {
          fetch(`delete.php?id=${id}`, { method: 'GET' })
              .then(response => {
                  if (!response.ok) throw new Error('Xóa không thành công');
                  return response.text();
              })
              .then(message => {
                  alert(message);
                  location.reload();
              })
              .catch(error => {
                  console.error('Delete error:', error);
                  alert('Xóa thất bại!');
              });
      }
  };
});