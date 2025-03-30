<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Social Media Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/profile.css" />
    <link rel="stylesheet" href="../css/edit_profile.css" />
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
                    <div id="search-result"></div>
                </div>
                <!-- End Navbar Search -->
                <!-- Navbar Action -->
                <ul class="header__action-action_list">
                    <li class="header__action-action_item" id="navbar-home">
                        <a style="background-color: #c9e2ff">
                            <i class="fas fa-home" style="color: #3080eb"></i>
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
            <div class="header__info">
                <img src="../img/default-avatar.png" alt="" class="header__infor-avatar" />
                <div class="wrapper">
                    <div class="card">
                        <img src="../img/default-avatar.png" alt="" />
                        <span>NguyenAnhTu</span>
                    </div>
                    <ul class="action_list">
                        <li class="action_item">
                            <div style="display: flex; align-items: center">
                                <img src="../img/setting.png" alt="" />
                                <span>Settings & privacy</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </li>
                        <li class="action_item">
                            <div style="display: flex; align-items: center">
                                <img src="../img/favorite.png" alt="" />
                                <span>Favorite</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </li>
                        <li class="action_item" id="navbar-logout">
                            <div style="display: flex; align-items: center">
                                <img src="../img/logout.png" alt="" />
                                <span>Logout</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- End Navbar Infor User -->
        </div>
        <!-- Profile Page -->
        <div class="profile-container">
            <div class="cover-img">
                <img src="../img/default_bg.jpg" alt="" />
                <input type="file" class="cover-btn" id="cover-photo-upload" style="display: none" />
                <label for="cover-photo-upload" class="cover-btn">
                    <i class="fas fa-camera-retro"></i> <span>Add Cover Photo</span>
                </label>
            </div>
            <div class="profile-details">
                <div class="pd-left">
                    <div class="pd-row">
                        <div class="pd-images">
                            <img src="../img/default-avatar.png" alt="" class="avatar" />
                            <input type="file" class="avatar-btn" id="avatar-upload" style="display: none" />
                            <label for="avatar-upload" class="avatar-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                        <div>
                            <h3>Nguyen Anh Tu</h3>
                            <p>120 Friends - 20 mutual</p>
                            <img src="../img/default-avatar.png" alt="" />
                            <img src="../img/default-avatar.png" alt="" />
                            <img src="../img/default-avatar.png" alt="" />
                            <img src="../img/default-avatar.png" alt="" />
                        </div>
                    </div>
                </div>
                <div class="pd-right">
                    <!-- <button type="button" class="add-friends-btn"><img src="../img/add-friends.png" alt="" />Add</button>
            <button type="button" class="friend-btn"><img src="../img/my-friend.png" alt="" />Friend</button>
            <button type="button" class="message-btn"><img src="../img/message.png" alt="" />Message</button> -->
                    <button type="button" class="edit-btn"><img src="../img/edit-profile.png" alt="" /> Edit</button>
                    <br />
                    <a class="more-btn"><img src="../img/more.png" alt="" /></a>
                </div>
            </div>
            <div class="profile-info">
                <div class="info-col">
                    <div class="profile-intro">
                        <h3>Intro</h3>
                        <p class="intro-text">Believe in yourself and you can do everything.</p>
                        <hr />
                        <ul>
                            <li><img src="../img/profile-job.png" alt="" />Director at Siuuu</li>
                            <li><img src="../img/profile-study.png" alt="" />Studied at Ha Noi University</li>
                            <li><img src="../img/profile-home.png" alt="" />Went to aaaa</li>
                        </ul>
                    </div>

                    <div class="profile-intro">
                        <div class="title-box">
                            <h3>Photos</h3>
                            <a href="">All Photos</a>
                        </div>
                        <div class="photo-box">
                            <div><img src="../img/default-avatar.png" alt="" /></div>
                            <div><img src="../img/default-avatar.png" alt="" /></div>
                            <div><img src="../img/default-avatar.png" alt="" /></div>
                            <div><img src="../img/default-avatar.png" alt="" /></div>
                            <div><img src="../img/default-avatar.png" alt="" /></div>
                            <div><img src="../img/default-avatar.png" alt="" /></div>
                        </div>
                    </div>

                    <div class="profile-intro">
                        <div class="title-box">
                            <h3>Friends</h3>
                            <a href="">All Friends</a>
                        </div>
                        <p>120 (10 mutual)</p>
                        <div class="friends-box">
                            <div>
                                <img src="../img/default-avatar.png" alt="" />
                                <p>xuan nhat</p>
                            </div>
                            <div>
                                <img src="../img/default-avatar.png" alt="" />
                                <p>hong phuc</p>
                            </div>
                            <div>
                                <img src="../img/default-avatar.png" alt="" />
                                <p>manh gat</p>
                            </div>
                            <div>
                                <img src="../img/default-avatar.png" alt="" />
                                <p>Antony</p>
                            </div>
                            <div>
                                <img src="../img/default-avatar.png" alt="" />
                                <p>Bradon</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="post-col">
                    <div class="post">
                        <!-- Create Post -->
                        <div class="create__post">
                            <div class="create__post-header">
                                <div class="create__post-profile_img">
                                    <img src="../img/default-avatar.png" alt="" />
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
                                <!-- Bai viet ban than -->
                                <section class="post__box-post">
                                    <div class="post__box-header">
                                        <div class="post__box-details">
                                            <a href="$profileUrl">
                                                <img src="../img/default-avatar.png" alt="$username" />
                                            </a>
                                            <div class="post__box-info">
                                                <a href="$profileUrl">
                                                    <span>Anhtu</span>
                                                </a>
                                                <div>18/10/1999</div>
                                            </div>
                                        </div>
                                        <div class="menu-btn">
                                            <span><i class="ri-more-line"></i></span>
                                        </div>
                                    </div>
                                    <div class="post__box-media_list">
                                        <img src="../img/default_bg.jpg" alt="Post Media" />
                                    </div>
                                    <div class="post__box-emotion">
                                        <div class="post__box-like">
                                            <i class="fas fa-heart" style="color: red"></i>
                                            <span>8</span>
                                        </div>
                                        <div class="post__box-emotion_right">
                                            <div class="post__box-comment">2 comments</div>
                                            <div class="post__box-share">2 shares</div>
                                        </div>
                                    </div>
                                    <ul class="post__box-interaction_list">
                                        <li class="post__box-interaction_item like-btn">
                                            <span><i class="far fa-heart"></i> Like</span>
                                        </li>
                                        <li class="post__box-interaction_item comment-btn">
                                            <span><i class="far fa-comment"></i> Comment</span>
                                        </li>
                                        <li class="post__box-interaction_item share-btn">
                                            <span><i class="far fa-share-square"></i> Share</span>
                                        </li>
                                    </ul>
                                </section>
                                <!-- Bai viet minh chia se -->
                                <section class="post__box-post">
                                    <div class="post__box-header">
                                        <div class="post__box-details">
                                            <a href="$profileUrl">
                                                <img src="../img/default-avatar.png" alt="$username" />
                                            </a>
                                            <div class="post__box-info">
                                                <a href="$profileUrl">
                                                    <span>Anhtu</span>
                                                </a>
                                                <div>18/10/1999</div>
                                            </div>
                                        </div>
                                        <div class="menu-btn">
                                            <span><i class="ri-more-line"></i></span>
                                        </div>
                                    </div>
                                    <div class="post__box-content">
                                        <div class="post__box-media_list" style="margin: 0">
                                            <img src="../img/default_bg.jpg" alt="Post Media" />
                                        </div>
                                        <div class="post__box-details" style="margin: 0 10px">
                                            <a href="$profileUrl">
                                                <img src="../img/default-avatar.png" alt="$username" />
                                            </a>
                                            <div class="post__box-info">
                                                <a href="$profileUrl">
                                                    <span>Anhtu</span>
                                                </a>
                                                <div>18/10/1999</div>
                                            </div>
                                        </div>
                                        <p style="margin: 10px">
                                            CHÀO MỪNG NGÀY NHÀ GIÁO VIỆT NAM 20-11 Bright Future tổ chức cuộc thi giảng
                                            dạy "CÔ BÊN EM"- one
                                            short. Cùng bình chọn cho video bài giảng ấn tượng nhất của các
                                        </p>
                                    </div>
                                </section>
                            </form>
                        </ul>
                        <!-- End Post List -->
                    </div>
                </div>
            </div>
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
    <div class="dialog-container"></div>
    <!-- End Dialog -->
    <div id="edit-profile-modal"></div>
</body>
<script src="../js/profile-handler.js"></script>

</html>