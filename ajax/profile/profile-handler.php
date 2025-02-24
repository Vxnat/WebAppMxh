<?php
require_once '../db_connection.php';

// Lấy dữ liêụ của người dùng
if (isset($_POST['get_user_profile'])) {
    $user_id = $_POST['user_id'];

    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $avatar = $row['avatar'] ?: '../img/default-avatar.png';
        $background = $row['bg_image'] ?: '../img/default_bg.jpg';
        $name = htmlspecialchars($row['full_name']);
        $friends_count = 120; // Giả lập số bạn bè, có thể lấy từ database
        $mutual_friends = 20; // Giả lập số bạn chung
        $bio = htmlspecialchars($row['bio']) ?: "Believe in yourself and you can do everything.";
        $location = htmlspecialchars($row['location']) ?: "Unknown location";
        $birthday = htmlspecialchars($row['birthday']) ?: "Not provided";

        echo '
            <div class="cover-img">
                <img src="' . $background . '" alt="Cover Photo" />
                <input type="file" class="cover-btn" id="cover-photo-upload" style="display: none" />
                <label for="cover-photo-upload" class="cover-btn">
                    <i class="fas fa-camera-retro"></i> <span>Add Cover Photo</span>
                </label>
            </div>
            <div class="profile-details">
                <div class="pd-left">
                    <div class="pd-row">
                        <div class="pd-images">
                            <img src="' . $avatar . '" alt="Avatar" class="avatar" />
                            <input type="file" class="avatar-btn" id="avatar-upload" style="display: none" />
                            <label for="avatar-upload" class="avatar-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                        <div>
                            <h3>' . $name . '</h3>
                            <p>' . $friends_count . ' Friends - ' . $mutual_friends . ' mutual</p>
                        </div>
                    </div>
                </div>
                <div class="pd-right">
                    <button type="button" class="edit-btn"><img src="../img/edit-profile.png" alt="Edit" /> Edit</button>
                </div>
            </div>
            <div class="profile-info">
                <div class="info-col">
                    <div class="profile-intro">
                        <h3>Intro</h3>
                        <p class="intro-text">' . $bio . '</p>
                        <hr />
                        <ul>
                            <li><img src="../img/birthday.png" alt="" />' . $birthday . '</li>
                            <li><img src="../img/profile-home.png" alt="" />' . $location . '</li>
                        </ul>
                    </div>
                </div>
            </div>';
    } else {
        echo '<p>Không tìm thấy thông tin người dùng.</p>';
    }
}
