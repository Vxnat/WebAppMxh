fetch('fetch.php')
// fetch(`delete.php?id=${id}`)

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('navbar-search');
    const searchResult = document.getElementById('search-result');

    searchInput.addEventListener('input', () => {
        const keyword = searchInput.value;
        if (keyword) {
            fetch(`search.php?q=${keyword}`)
                .then(response => response.json())
                .then(data => {
                    searchResult.innerHTML = data.map(item => `<div>${item.title}</div>`).join('');
                    searchResult.classList.add('active');
                });
        } else {
            searchResult.classList.remove('active');
        }
    });

    fetch('fetch.php')
        .then(response => console.log(response.json())
        )
        // .then(data => {
        //     // const content = document.querySelector('.content');
        //     // content.innerHTML = data.map(item => `
        //     //     <a href="#" class="saved-item">
        //     //         <img src="${item.image}" alt="${item.title}">
        //     //         <div class="item-info">
        //     //             <h3>${item.title}</h3>
        //     //             ${item.type === 'Video' ? `<p>Video • ${item.duration}</p>` : '<p>Bài viết</p>'}
        //     //             <button class="btn">Thêm vào bộ sưu tập</button>
        //     //         </div>
        //     //     </a>
        //     // `).join('');
        // });
});
document.getElementById('avatar-btn').addEventListener('click', function () {
    document.getElementById('dropdown-menu').classList.toggle('active');
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.avatar-btn') && !e.target.closest('.dropdown-menu')) {
        document.getElementById('dropdown-menu').classList.remove('active');
    }
});
// document.addEventListener('DOMContentLoaded', () => {
//     const articleList = document.getElementById('article-list');

//     // Hiển thị danh sách bài viết
//     fetch('fetch.php')
//         .then(response => response.json())
//         .then(data => {
//             articleList.innerHTML = data.map(item => `
//                 <div class="saved-item">
//                     <div class="item-info">
//                         <h3>Post ID: ${item.post_id}</h3>
//                         <p>User ID: ${item.user_id}</p>
//                         <p>Saved at: ${item.saved_at}</p>
//                         <button class="btn" onclick="deleteArticle(${item.saved_id})">Bỏ lưu</button>
//                     </div>
//                 </div>
//             `).join('');
//         });

//     // Xóa bài viết
//     window.deleteArticle = function (id) {
//         if (confirm('Bạn có chắc muốn xóa bài viết này không?')) {
//             fetch(`delete.php?id=${id}`, { method: 'GET' })
//                 .then(response => {
//                     if (!response.ok) throw new Error('Xóa không thành công');
//                     return response.text();
//                 })
//                 .then(message => {
//                     alert(message);
//                     location.reload();
//                 })
//                 .catch(error => {
//                     console.error('Delete error:', error);
//                     alert('Xóa thất bại!');
//                 });
//         }
//     };
    
// });
