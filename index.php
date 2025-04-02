<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="fetch.css">
    <script src="app.js" defer></script>

</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header__action">
                <div class="header__action-logo">
                    <img src="https://images-platform.99static.com//eMcLXWfD7Bjw35rfjApyxx4YXMI=/416x164:1061x809/fit-in/500x500/99designs-contests-attachments/65/65104/attachment_65104401"
                        alt="" />
                    <span>Peace</span>
                </div>
                <div class="header__action-search">
                    <div class="search__container">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search for friends" aria-label="Search" id="navbar-search" />
                    </div>
                    <div id="search-result"></div>
                </div>
                <ul class="header__action-action_list">
                    <li class="header__action-action_item" id="navbar-home">
                        <a style="background-color: #c9e2ff">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li class="header__action-action_item" id="navbar-chat">
                        <a href="#">
                            <i class="far fa-comment"></i>
                        </a>
                    </li>
                    <li class="header__action-action_item" id="navbar-noti">
                        <a>
                            <i class="far fa-bell"></i>
                        </a>
                        <div class="new-noti"></div>
                        <div class="header__notify">
                            <header class="header__notify-header">Notifications</header>
                            <form method="post" id="notify-list"></form>
                        </div>
                    </li>
                </ul>

            </div>
            <button class="avatar-btn" id="avatar-btn">
                <img src="img/tải xuống.jpg" alt="Avatar">
            </button>
            <div class="dropdown-menu" id="dropdown-menu">
                <div class="dropdown-header">
                    <p class="username">Trần Đức Mạnh</p>
                    <button class="profile-btn">Xem tất cả trang cá nhân</button>
                </div>
                <a href="#"><i class="fas fa-cog"></i> Cài đặt và quyền riêng tư</a>
                <a href="#"><i class="fas fa-question-circle"></i> Trợ giúp & hỗ trợ</a>
                <a href="#"><i class="fas fa-moon"></i> Màn hình & trợ năng</a>
                <a href="#"><i class="fas fa-comment-alt"></i> Đóng góp ý kiến</a>
                <a href="#"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>
        <div class="saved-container">
            <aside class="sidebar">
                <h2>Đã lưu</h2>
                <a href="#" class="saved-menu">
                    <i class="fas fa-bookmark"></i> Mục đã lưu
                </a>
                <h3>Bộ sưu tập của tôi</h3>
                <a href="#" class="collection">
                    <img src="img/tải xuống.jpg" alt="Avatar">
                    <div>
                        <p>Để xem sau</p>
                        <span>Chỉ mình tôi</span>
                    </div>
                </a>
            </aside>

            <main class="content">
                <h1>Danh sách bài viết đã lưu</h1>
                <ul id="saved-posts">
                    <?php include 'fetch.php'; ?>
                </ul>

            </main>
        </div>
    </div>

</body>

</html>