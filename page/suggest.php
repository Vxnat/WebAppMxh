<?php
  include("../includes/check_login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết nối bạn bè</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- CSS -->
    <link rel="stylesheet" href="../css/suggest.css">
    <!-- iCON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <div class="bard-sidenav-container">
        <div class="bard-sidenav">
            <div class="header">
                <a href="javascript:history.back()">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="header-title">
                    <a href="">
                        <p>Bạn bè</p>
                    </a>
                    <h2>Gợi ý kết bạn</h2>
                </div>
            </div>

            <div class="suggestion-title">
                <p>Những người bạn có thể biết</p>
            </div>
            <!--Hiển thị danh sách gợi ý kết bạn-->
            <ul class="suggestions-list">
            </ul>

        </div>

        <div class="bard-sidenav-content">
            <div class="content-container">
                <h2>Lời mời kết bạn</h2>
                <!-- Hiển thị danh sách gợi ý kết bạn -->
                <ul class="friend-request-list">
                </ul>
            </div>
        </div>
    </div>
</body>
<script src="../js/suggest.js"></script>

</html>