document.addEventListener('DOMContentLoaded', () => {
    const articleList = document.getElementById('article-list');

    // Hiển thị danh sách bài viết
    fetch('fetch.php')
        .then(response => response.json())
        .then(data => {
            articleList.innerHTML = data.map(item => `
                <div class="saved-item">
                    <div class="item-info">
                        <h3>Post ID: ${item.post_id}</h3>
                        <p>User ID: ${item.user_id}</p>
                        <p>Saved at: ${item.saved_at}</p>
                        <button class="btn" onclick="deleteArticle(${item.saved_id})">Bỏ lưu</button>
                    </div>
                </div>
            `).join('');
        });

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
