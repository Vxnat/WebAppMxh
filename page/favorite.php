<?php
  include("../includes/check_login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/favorite.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/fetch.css">
    <script src="../js/savePost.js" defer></script>

</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header__action">
                <!-- Navbar Logo -->
                <div class="header__action-logo">
                    <img src="https://images-platform.99static.com//eMcLXWfD7Bjw35rfjApyxx4YXMI=/416x164:1061x809/fit-in/500x500/99designs-contests-attachments/65/65104/attachment_65104401"
                        alt="" />
                    <span>Peace</span>
                </div>
                <!-- End Navbar Logo -->
                <!-- Navbar Search -->
                <div class="header__action-search">
                    <div class="search__container">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search for friends" aria-label="Search" id="navbar-search" />
                    </div>
                    <div id="search-result">
                    </div>
                </div>
                <!-- End Navbar Search -->
                <!-- Navbar Action -->
                <ul class="header__action-action_list">
                    <li class="header__action-action_item" id="navbar-home">
                        <a href="home.php" style="background-color: #c9e2ff">
                            <i class="fas fa-home" style="color: #3080eb"></i>
                        </a>
                    </li>
                    <li class="header__action-action_item" id="navbar-chat">
                        <a href="message.php">
                            <i class="far fa-comment"></i>
                        </a>
                    </li>
                    <li class="header__action-action_item" id="navbar-noti">
                        <a>
                            <i class="far fa-bell"></i>
                        </a>
                        <div class="new-noti"></div>
                        <!-- Notify Container -->
                        <div class="header__notify">
                            <header class="header__notify-header">Notifications</header>
                            <form method="post" id="notify-list"></form>
                        </div>
                        <!-- End Notify Container -->
                    </li>
                </ul>
                <!-- End Navbar Action -->
            </div>
            <!-- Navbar Info User -->
            <div class="header__info" data-user-id=<?=$_SESSION["user_id"] ?>>
                <?=$logined ?>
                <div class="wrapper">
                    <a href="profile.php?user_id=<?=$_SESSION['user_id'] ?>">
                        <div class="card">
                            <img src=<?=$_SESSION['avatar'] ?> alt="">
                            <span><?=$_SESSION['full_name'] ?></span>
                        </div>
                    </a>
                    <ul class="action_list">
                        <a href="../page/setting.php">
                            <li class="action_item">
                                <div style="display: flex; align-items: center;">
                                    <img src="../img/setting.png" alt="">
                                    <span>Settings & privacy</span>
                                </div>
                                <i class="fa-solid fa-chevron-right"></i>
                            </li>
                        </a>
                        <a href="favorite.php">
                            <li class="action_item">
                                <div style="display: flex; align-items: center;">
                                    <img src="../img/favorite.png" alt="">
                                    <span>Favorite</span>
                                </div>
                                <i class="fa-solid fa-chevron-right"></i>
                            </li>
                        </a>
                        <li class="action_item" id="navbar-logout">
                            <div style="display: flex; align-items: center;">
                                <img src="../img/logout.png" alt="">
                                <span>Logout</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- End Navbar Infor User -->
        </div>
        <div class="saved-container">
            <aside class="sidebar">
                <h2>Đã lưu</h2>
                <a href="#" class="saved-menu">
                    <i class="fas fa-bookmark"></i> Mục đã lưu
                </a>
                <h3>Bộ sưu tập của tôi</h3>
                <a href="#" class="collection">
                    <img src=<?=$_SESSION['avatar'] ?> alt="">
                    <div>
                        <p>Để xem sau</p>
                        <span>Chỉ mình tôi</span>
                    </div>
                </a>
            </aside>

            <main class="content-saved">
                <h1>Danh sách bài viết đã lưu</h1>
                <ul id="saved-posts">
                    <?php include '../ajax/save-post/fetch.php'; ?>
                </ul>

            </main>
        </div>
    </div>
</body>
<script src="../js/index.js"></script>
<script src="../js/home/search-handler.js"></script>
<script src="../js/home/noti-handler.js"></script>
<script src="../js/home/home-global.js"></script>
<script src="../js/extension/extension.js"></script>
<script src="../js/profile-handler.js"></script>

</html>