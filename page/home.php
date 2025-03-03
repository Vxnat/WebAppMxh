<?php
  include("../includes/check_login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Social Media Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/video.css" />
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
                    <div class="card">
                        <img src=<?=$_SESSION['avatar'] ?> alt="">
                        <span><?=$_SESSION['full_name'] ?></span>
                    </div>
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
                        <li class="action_item">
                            <div style="display: flex; align-items: center;">
                                <img src="../img/favorite.png" alt="">
                                <span>Favorite</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </li>
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
        <div class="content">
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Infor User -->
                <div class="sidebar__wrapper">
                    <form method="post" id="info-user">
                    </form>
                </div>
                <!-- End Infor User -->
                <!-- Shortcut -->
                <div class="sidebar__wrapper">
                    <div class="sidebar__wrapper-header">
                        <strong>Your shortcuts</strong>
                    </div>
                    <form id="shortcut-list">
                    </form>
                </div>
                <!-- End Shortcut -->
            </div>
            <!-- End Sidebar -->
            <!-- Main Content -->
            <div class="main__content">
                <!-- Post -->
                <div class="post">
                    <!-- Create Post -->
                    <div class="create__post">
                        <div class="create__post-header">
                            <div class="create__post-profile_img">
                                <img src=<?=$_SESSION["avatar"]?> alt="">
                            </div>
                            <input type="text" placeholder="What's on your mind?" readonly />
                        </div>
                        <ul class="create__post-action_list">
                            <li class="create__post-action_item">
                                <a href="#"><i class="fas fa-video" style="color: #e42645"></i>Video</a>
                            </li>
                            <li class="create__post-action_item">
                                <a href="#"><i class="fas fa-image" style="color: #45bd62"></i>Photo</a>
                            </li>
                        </ul>
                    </div>
                    <!-- End Create Post -->
                    <!-- Post List -->
                    <ul class="post__list">
                        <form method="post" id="post-list-form">
                        </form>
                    </ul>
                    <!-- End Post List -->
                </div>
                <!-- Right Sidebar -->

                <!-- End Right Sidebar -->
            </div>
            <div class="right__sidebar">
                <!-- Activity Container -->
                <div class="sidebar__wrapper">
                    <div class="sidebar__wrapper-header">
                        <strong>Activity</strong>
                    </div>
                    <div class="sidebar__wrapper-content">
                        <ul class="sidebar__wrapper-list">
                            <form method="post" id="activity-list">
                            </form>
                        </ul>
                    </div>
                </div>
                <!-- End Activity Container -->
                <!-- Suggested Container -->
                <div class="sidebar__wrapper">
                    <div class="sidebar__wrapper-header">
                        <strong>Suggested for you</strong>
                        <a href="#">See all</a>
                    </div>
                    <form id="suggest-list">
                    </form>
                </div>
                <!-- End Suggested Container -->
            </div>
            <!-- End Main Content -->
        </div>
    </div>
    <!-- Overlay -->
    <div class="overlay">
        <div class="spinner"></div>
    </div>
    <!-- End Overlay -->
    <!-- Modal Post -->
    <div id="post-modal" class="modal">
        <div class="modal-content">
            <div id="modal-post-content"></div>
        </div>
    </div>
    <!-- End Modal Post -->
    <!-- Dialog -->
    <div class="dialog-container">
    </div>
    <!-- End Dialog -->
</body>
<script src="../js/index.js"></script>
<script src="../js/config/cloudinary-config.js"></script>
<script src="../js/home/search-handler.js"></script>
<script src="../js/home/post-handler.js"></script>
<script src="../js/home/comment-handler.js"></script>
<script src="../js/home/noti-handler.js"></script>
<script src="../js/home/activity-handler.js"></script>
<script src="../js/home/suggest-handler.js"></script>
<script src="../js/home/shortcut-handler.js"></script>
<script src="../js/home/home-global.js"></script>
<script src="../js/home/custom-video.js"></script>
<script src="../js/config/emoji.js"></script>
<script src="../js/extension/extension.js"></script>

</html>