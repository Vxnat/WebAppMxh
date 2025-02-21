<?php
include("../includes/check_login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Setting</title>

    <!--font awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!--css file-->
    <link rel="stylesheet" href="../css/setting.css" />
</head>

<body>
    <div class="sidebar close">
        <div class="logo">
            <i class="fab fa-trade-federation"></i>
            <span class="logo-name">Setting</span>
        </div>

        <ul class="nav-list">
            <li id="home">
                <a href="../page/home.php">
                    <i class="fas fa-home"></i>
                    <span class="link-name">Home</span>
                </a>

                <ul class="sub-menu blank">
                    <li><a href="#" class="link-name">Home</a></li>
                </ul>
            </li>
            <li id="privacy">
                <a href="#">
                    <i class="fas fa-user-shield"></i>
                    <span class="link-name">Privacy</span>
                </a>

                <ul class="sub-menu blank">
                    <li><a href="#" class="link-name">Privacy</a></li>
                </ul>
            </li>
            <li id="password">
                <a href="#">
                    <i class="fas fa-unlock-alt"></i>
                    <span class="link-name">Password</span>
                </a>

                <ul class="sub-menu blank">
                    <li><a href="#" class="link-name">Password</a></li>
                </ul>
            </li>
            <!-- <li>
          <div class="icon-link">
            <a href="#">
              <i class="fab fa-blogger"></i>
              <span class="link-name">Blog</span>
            </a>
            <i class="fas fa-caret-down arrow"></i>
          </div>

          <ul class="sub-menu">
            <li><a href="#" class="link-name">Blog</a></li>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Card Design</a></li>
            <li><a href="#">Form Design</a></li>
          </ul>
        </li> -->

            <li>
                <div class="profile-details">
                    <div class="profile-content">
                        <img src="../img/default-avatar.png" alt="" />
                    </div>

                    <div class="name-job">
                        <div class="name"><?=$_SESSION['full_name']?></div>
                        <div class="job">User</div>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <div class="home-section">
        <div class="home-content">
            <!-- Privacy -->
            <div id="privacy-section">
            </div>
            <!-- End Privacy -->
            <!-- Password  -->
            <form id="change-password-form" style="display: none">
                <div class="form-group">
                    <label for="current-password">Mật khẩu hiện tại:</label>
                    <div class="password-input-container">
                        <input type="password" id="current-password" name="current-password" required />
                        <i class="fas fa-eye-slash toggle-password" data-target="current-password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new-password">Mật khẩu mới:</label>
                    <div class="password-input-container">
                        <input type="password" id="new-password" name="new-password" required />
                        <i class="fas fa-eye-slash toggle-password" data-target="new-password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Xác nhận mật khẩu mới:</label>
                    <div class="password-input-container">
                        <input type="password" id="confirm-password" name="confirm-password" required />
                        <i class="fas fa-eye-slash toggle-password" data-target="confirm-password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <button type="button" id="change-password-btn">Đổi mật khẩu</button>
                </div>
            </form>
            <!-- End Password -->
        </div>
    </div>

    <script src="../js/home/setting-handler.js"></script>
</body>

</html>