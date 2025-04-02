<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/tailwindcss-colors.css">
    <link rel="stylesheet" href="../css/message.css">
    <title>Chat</title>
</head>

<body>
    <!-- start: Chat -->
    <section class="chat-section">
        <div class="chat-container">
            <!-- start: Sidebar -->
            <aside class="chat-sidebar">
                <a href="#" class="chat-sidebar-logo">
                    <i class="ri-chat-1-fill"></i>
                </a>
                <ul class="chat-sidebar-menu">
                    <li><a href="home.php" data-title="Home"><i class="ri-home-4-line"></i></a></li>
                    <li class="active"><a href="#" data-title="Chats"><i class="ri-chat-3-line"></i></a></li>
                    <li class="chat-sidebar-profile" data-user-id=<?= $_SESSION['user_id'] ?>>
                        <button type="button" class="chat-sidebar-profile-toggle">
                            <img src=<?=$_SESSION['avatar'] ?> alt="">
                        </button>
                        <ul class="chat-sidebar-profile-dropdown">
                            <li><a href="#"><i class="ri-user-line"></i> Profile</a></li>
                            <li><a href="#"><i class="ri-logout-box-line"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </aside>
            <!-- end: Sidebar -->
            <!-- start: Content -->
            <div class="chat-content">
                <!-- start: Content side -->
                <div class="content-sidebar">
                    <div class="content-sidebar-title">
                        <p>Chats</p>
                        <button type="button" class="content-sidebar-add-chat add-group"><i
                                class="ri-group-line"></i></button>
                    </div>
                    <form action="" class="content-sidebar-form">
                        <input type="search" class="content-sidebar-input" placeholder="Search...">
                        <button type="button" class="content-sidebar-submit"><i class="ri-search-line"></i></button>
                    </form>
                    <div class="content-messages">
                        <ul class="content-messages-list person-chat">
                            <li class="content-message-title"><span>Tin nhắn trực tiếp</span></li>
                        </ul>
                        <ul class="content-messages-list group-chat">
                            <li class="content-message-title"><span>Nhóm chat</span></li>
                        </ul>
                    </div>
                </div>
                <!-- end: Content side -->
                <!-- start: Conversation -->
                <div class="conversation conversation-default active">
                    <i class="ri-chat-3-line"></i>
                    <p>Select chat and view conversation!</p>
                </div>
                <!-- Conversation Container -->
                <span class="conversation-container"></span>
            </div>
            <!-- end: Content -->
        </div>
    </section>
    <!-- end: Chat -->
    <div class='dialog-container'>
        <div class="wrapper">
        </div>
    </div>
</body>
<script src="../js/config/cloudinary-config.js"></script>
<script type="module" src="../js/config/firebase-chat.js"></script>
<script type="module" src="../js/message/message-handler.js"></script>

</html>