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
        .then(response => response.json())
        .then(data => {
            const content = document.querySelector('.content');
            content.innerHTML = data.map(item => `
                <a href="#" class="saved-item">
                    <img src="${item.image}" alt="${item.title}">
                    <div class="item-info">
                        <h3>${item.title}</h3>
                        ${item.type === 'Video' ? `<p>Video • ${item.duration}</p>` : '<p>Bài viết</p>'}
                        <button class="btn">Thêm vào bộ sưu tập</button>
                    </div>
                </a>
            `).join('');
        });
});
document.getElementById('avatar-btn').addEventListener('click', function () {
    document.getElementById('dropdown-menu').classList.toggle('active');
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.avatar-btn') && !e.target.closest('.dropdown-menu')) {
        document.getElementById('dropdown-menu').classList.remove('active');
    }
});