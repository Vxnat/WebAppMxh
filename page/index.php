<!DOCTYPE html>
<html lang="en">

<head>
<<<<<<< HEAD
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/index.css" />
    <title>Login Page</title>
</head>

<body>
    <div class="container" id="container">
        <!-- Sign UpUp -->
        <div class="form-container sign-up">
            <form method="post">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <!-- <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
                </div>
                <span>or use your email for registeration</span>
                <input type="email" placeholder="Email" id="su-email" />
                <input type="password" placeholder="Password" id="su-pw" />
                <input type="password" placeholder="Confirm Password" id="cf-pw" />
                <button type="button" id="su-btn">Sign Up</button>
            </form>
        </div>
        <!-- Sign In -->
        <div class="form-container sign-in">
            <form method="post">
                <h1>Sign In</h1>
                <div class="social-icons">
                    <!-- <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
                </div>
                <span>or use your email password</span>
                <input type="email" placeholder="Email" id="si-email" />
                <input type="password" placeholder="Password" id="si-pw" />
                <a href="forgot_password.php">Forget Your Password?</a>
                <button type="button" id="si-btn">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button type="button" class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button type="button" class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/index.js"></script>
=======
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/fetch.css">
    <script src="../js/savePost.js" defer></script>

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
                <img src="../img/tải xuống.jpg" alt="Avatar">
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
                    <img src="../img/tải xuống.jpg" alt="Avatar">
                    <div>
                        <p>Để xem sau</p>
                        <span>Chỉ mình tôi</span>
                    </div>
                </a>
            </aside>

            <main class="content">
                <h1>Danh sách bài viết đã lưu</h1>
                <ul id="saved-posts">
                    <?php include '../ajax/save-post/fetch.php'; ?>
                </ul>

            </main>
        </div>
    </div>

>>>>>>> origin/TranDucManh
</body>

</html>