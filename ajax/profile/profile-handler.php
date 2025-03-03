<?php
require_once '../db_connection.php';
session_start();

$_SESSION['user_id'] = 1; // Placeholder userId

// Lấy dữ liệu của người dùng
if (isset($_POST['get_user_profile'])) {
    // Id của trang cá nhân người dùng
    $profile_user_id = $_POST['profileUserId'];
    $current_user_id = $_SESSION['user_id']; // Lấy ID của người dùng đang đăng nhập

    // Kiem tra xem nguoi dung co phai la nguoi dung hien tai khong
    $isMe = $profile_user_id == $current_user_id;

    // Lấy thông tin người dùng
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $profile_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $html = '';

    if ($userData = mysqli_fetch_assoc($result)) {

        // Gọi hàm lấy danh sách bạn bè
        $friend_data = getUserFriends($profile_user_id, 6, $conn);

        // Trả về html profile người dùng
        $html .= getProfileUser($userData, $friend_data, $current_user_id, $profile_user_id, $isMe, $conn);

        // Trả về html danh sách các bài post của người dùng


        echo $html;
    } else {
        echo '<p>Không tìm thấy thông tin người dùng.</p>';
    }
}

// Trả về html profile người dùng
function getProfileUser($userData, $friend_data, $current_user_id, $profile_user_id, $isMe, $conn)
{
    $html = '';

    // Thông tin cơ bản
    $avatar = $userData['avatar'] ?: '../img/default-avatar.png';
    $background = $userData['bg_image'] ?: '../img/default-bg.jpg';
    $name = htmlspecialchars($userData['full_name']);
    $bio = htmlspecialchars($userData['bio']) ?: "Believe in yourself and you can do everything.";
    $location = htmlspecialchars($userData['location']) ?: "Unknown location";
    $birthday = htmlspecialchars($userData['birthday']) ?: "Not provided";
    // Thông tin bạn bè
    $friends = $friend_data['friends'];
    $total_friends = count($friends); // Đếm số bạn bè hiển thị

    // Hiển thị danh sách bạn bè
    $friends_html = '';
    foreach ($friends as $friend) {
        $friends_html .= '
              <a href="profile.php?user_id=' . $friend['user_id'] . '"> 
              <div>
                  <img src="' . $friend['avatar'] . '" alt="" />
                  <p>' . $friend['name'] . '</p>
              </div></a>';
    }

    // Hiển thị nút chức năng tương ứng 
    $buttons = getFriendshipButtons($current_user_id, $profile_user_id, $isMe, $conn);

    // Xuất dữ liệu HTML
    $html .= '
          <div class="cover-img">
              <img src="' . $background . '" alt="Cover Photo" class="background-img" />';
    // Nếu là bản thân thì hiện nút thay đổi bg
    if ($isMe) {
        $html .= '<input type="file" accept="image/jpeg, image/png, image/jpg" class="cover-btn" id="cover-photo-upload" style="display: none" />
              <label for="cover-photo-upload" class="cover-btn">
                  <i class="fas fa-camera-retro"></i> <span>Add Cover Photo</span>
              </label>';
    }

    $html .= '
      </div>
          <div class="profile-details">
              <div class="pd-left">
                  <div class="pd-row">
                      <div class="pd-images">
                          <img src="' . $avatar . '" alt="Avatar" class="avatar" />';

    // Nếu là bản thân thì hiện nút thay doi avatar
    if ($isMe) {
        $html .= '<input type="file" accept="image/jpeg, image/png, image/jpg" class="avatar-btn" id="avatar-upload" style="display: none" />
                          <label for="avatar-upload" class="avatar-btn">
                              <i class="fas fa-camera"></i>
                          </label>';
    }

    $html .=  '</div>
                      <div>
                          <h3>' . $name . '</h3>
                      </div>
                  </div>
              </div>
              <div class="pd-right">' . $buttons . '</div>
          </div>
          <div class="profile-info">
              <div class="info-col">
                  <div class="profile-intro">
                      <h3>Intro</h3>
                      <p class="intro-text">' . $bio . '</p>
                      <hr />
                      <ul>
                          <li><img src="../img/profile-study.png" alt="" />' . $birthday . '</li>
                          <li><img src="../img/profile-home.png" alt="" />' . $location . '</li>
                      </ul>
                  </div>
                  <div class="profile-intro">
                  <div class="title-box">
                    <h3>Friends</h3>
                    <a href="#" id="all-friend-btn">All Friends</a>
                  </div>
                  <p>' . $total_friends . ' Friends</p>
                  <div class="friends-box">' . $friends_html . '</div>
                </div>
              </div>
              <div class="post-col">
                <ul class="post__list">
                  <form method="post" id="post-list-form">
                  </form>
                </ul>
              </div>
          </div>';

    return $html;
}

// Lấy danh sách bạn bè của trang cá nhân hiện tại
function getUserFriends($profile_user_id, $limit, $conn)
{
    $friends = [];

    // Lấy danh sách bạn bè của user_id
    $query = "
      SELECT u.user_id, u.full_name, u.avatar 
      FROM Friendships f
      JOIN Users u ON (f.user_id = u.user_id OR f.friend_id = u.user_id)
      WHERE (f.user_id = ? OR f.friend_id = ?) 
      AND u.user_id != ?
      AND f.status = 'accepted'
      ";

    if (isset($limit)) {
        $query .= "LIMIT $limit";
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $profile_user_id, $profile_user_id, $profile_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $friends[] = [
            'user_id' => $row['user_id'],
            'name' => htmlspecialchars($row['full_name']),
            'avatar' => $row['avatar'] ?: '../img/default-avatar.png'
        ];
    }

    return ['friends' => $friends];
}

// Kiểm tra và trả về nút chức năng trong trang cá nhân người dùng
function getFriendshipButtons($current_user_id, $profile_user_id, $isMe, $conn)
{
    // Kiểm tra trạng thái quan hệ bạn bè
    $friend_status = 'stranger'; // Mặc định là người lạ

    if ($isMe) {
        $friend_status = 'self'; // Người dùng đang xem chính họ
    } else {
        $friend_query = "SELECT user_id, friend_id, status FROM Friendships
    WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
        $stmt_friend = mysqli_prepare($conn, $friend_query);
        mysqli_stmt_bind_param($stmt_friend, "iiii", $current_user_id, $profile_user_id, $profile_user_id, $current_user_id);
        mysqli_stmt_execute($stmt_friend);
        $result_friend = mysqli_stmt_get_result($stmt_friend);

        if ($row_friend = mysqli_fetch_assoc($result_friend)) {
            if ($row_friend['status'] == 'accepted') {
                $friend_status = 'friend'; // Nếu đã là bạn bè
            } else if ($row_friend['status'] == 'pending') {
                if ($row_friend['user_id'] == $current_user_id) {
                    $friend_status = 'request_sent'; // Người dùng hiện tại đã gửi lời mời kết bạn
                } else {
                    $friend_status = 'request_received'; // Người dùng hiện tại nhận được lời mời kết bạn
                }
            }
        }
    }

    // Phần nút tương ứng khi người dùng là bản thân hoặc bạn bè hoặc người lạ hoặc người mình đã gửi lời mời kết bạn
    $buttons = '';
    if ($friend_status == 'self') {
        $buttons = '<button type="button" class="edit-btn"><i class="ri-pencil-fill"></i>Edit</button>';
    } elseif ($friend_status == 'friend') {
        $buttons = '
            <button type="button" class="friend-btn"><i class="ri-user-follow-fill"></i>Friend</button>
            <button type="button" class="message-btn"><i class="ri-chat-3-fill"></i>Message</button>';
    } else if ($friend_status == 'request_sent') {
        $buttons = '
            <button type="button" class="cancel-friend-request-btn"><i class="ri-user-unfollow-fill"></i>Cancel request</button>
            <button type="button" class="message-btn"><i class="ri-chat-3-fill"></i>Message</button>';
    } else if ($friend_status == 'request_received') {
        $buttons = '
            <button type="button" class="accept-friend-request-btn" style="background-color: green; color: white"><i class="ri-user-heart-line"></i>Accept</button>
            <button type="button" class="decline-friend-request-btn" style="background-color: red"><i class="ri-user-unfollow-fill"></i>Decline</button>
            <button type="button" class="message-btn"><i class="ri-chat-3-fill"></i>Message</button>';
    } else {
        $buttons = '
            <button type="button" class="add-friends-btn"><i class="ri-user-add-fill"></i>Add Friend</button>
            <button type="button" class="message-btn"><i class="ri-chat-3-fill"></i>Message</button>';
    }

    return $buttons;
}

// Thay đổi ảnh bìa của người dùng
if (isset($_POST['change_cover_photo'])) {
    $current_user_id = $_POST['user_id'];
    $new_cover_photo = $_POST['background_url'];

    $query = "UPDATE users SET bg_image = '$new_cover_photo' WHERE user_id = '$current_user_id'";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

// Thay đổi ảnh avatar của người dùng
if (isset($_POST['change_avatar'])) {
    $current_user_id = $_POST['user_id'];
    $new_avatar = $_POST['avatar_url'];

    $query = "UPDATE users SET avatar = '$new_avatar' WHERE user_id = '$current_user_id'";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

// Lấy dữ liệu edit form của người dùng
if (isset($_POST['get_edit_profile_content'])) {
    $user_id = $_SESSION['user_id'];

    // Tránh SQL Injection bằng cách sử dụng prepared statements
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $output = '';

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
        $background = !empty($row['bg_image']) ? $row['bg_image'] : '../img/default-bg.jpg';
        $location = htmlspecialchars($row['location']);
        $birthday = htmlspecialchars($row['birthday']);
        $bio = htmlspecialchars($row['bio']);
        $name = htmlspecialchars(string: $row['full_name']);

        $output .= '
        <div class="edit">
          <div class="header-edit">
            <b>Chỉnh sửa trang cá nhân</b>
            <i class="fa-solid fa-x"></i>
          </div>
          <hr />
          <div class="avatar">
            <div class="avatar_b">
              <b>Ảnh đại diện</b>
            </div>
            <img src="' . $avatar . '" alt="Avatar" />
          </div>
          <div class="background">
            <div class="background_b">
              <b>Ảnh bìa</b>
            </div>
            <img src="' . $background . '" alt="Background Image"  />
          </div>
          <div class="bio">
            <div class="bio_b">
              <b>Tiểu sử</b>
            </div>
            <div class="bio-content">
              <input type="text" id="bio-input" value="' . $bio . '" placeholder="Mô tả bản thân..." />
            </div>
          </div>
          <div class="edit_introduction">
            <div class="edit_introduction_b">
              <b>Chỉnh sửa phần giới thiệu</b>
            </div>
            <div class="edit_introduction_c">
              <i class="fa-solid fa-user"></i>
              <input type="text" value="' . $name . '" placeholder="Tên" class="edit-input" />
            </div>
            <div class="edit_introduction_c">
              <i class="fa-regular fa-calendar-days"></i>
              <input type="date" value="' . $birthday . '" placeholder="Ngày sinh" class="edit-input" />
            </div>
            <div class="edit_introduction_c">
              <i class="fa-solid fa-location-dot"></i>
              <input type="text" value="' . $location . '" placeholder="Địa chỉ" class="edit-input" />
            </div>
          </div>
          <div class="button-container">
            <button type="button" class="save-btn" id="save-profile-btn">Save</button>
          </div>
        </div>';
    }

    echo $output;
}

// Thực hiện lưu những thay đổi từ edit form
if (isset($_POST['save_edit_profile'])) {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $birthday = $_POST['birthday'];
    $location = $_POST['location'];
    $bio = $_POST['bio'];

    // Cập nhật dữ liệu vào CSDL
    $query = "UPDATE users SET full_name ='$name', birthday='$birthday', location='$location', bio='$bio' 
            WHERE user_id='$user_id'";

    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo 'failed';
    }
}

// Lấy về tất cả bạn bè
if (isset($_POST['get_all_friends'])) {
    $profile_user_id = $_POST['profileUserId'];
    $current_user_id = $_SESSION['user_id'];

    // Gọi hàm lấy danh sách bạn bè
    $friend_data = getUserFriends($profile_user_id, null, $conn);
    $friends = $friend_data['friends'];
    $total_friends = count($friends); // Đếm số bạn bè hiển thị

    $friends_html = '<ul class="friend-list">';
    foreach ($friends as $friend) {
        $friends_html .= '
            <li><a href="profile.php?user_id=' . $friend['user_id'] . '"> 
            <div>
                <img src="' . $friend['avatar'] . '" alt="" />
                <p>' . $friend['name'] . '</p>
            </div></a></li>';
    }

    $friends_html .= '</ul>';

    echo $friends_html;
}

// Chức năng gửi lời mời kết bạn
if (isset($_POST['add_friend'])) {
    $current_user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Kiểm tra xem 2 người đó đã kết bạn chưa
    $query = "SELECT * FROM friendships
  WHERE (user_id = $current_user_id AND friend_id = $friend_id)
  OR (user_id = $friend_id AND friend_id = $current_user_id)";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false]);
        exit;
    }

    // Thêm kết ban với người dùng 
    $query = "INSERT INTO friendships (user_id, friend_id, status) VALUES ($current_user_id, $friend_id, 'pending')";
    sendNotiToUser($current_user_id, $friend_id, 'addfriend', $conn);
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

// Chức năng xóa lời mời kết bạn mình đã gửi
if (isset($_POST['cancel_friend_request'])) {
    $current_user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Xóa lời mời kết ban mình gửi
    $query = "DELETE FROM friendships WHERE user_id = $current_user_id AND friend_id = $friend_id AND status = 'pending'";
    $deleteNotiQuery = "DELETE FROM notifications WHERE user_id = $friend_id AND notification_type = 'friend_request';";
    if (mysqli_query($conn, $query) && mysqli_query($conn, $deleteNotiQuery)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

// Chức năng hủy kết bạn
if (isset($_POST['remove_friend'])) {
    $current_user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Xóa kết bạn từ người dùng đang đăng nhập và bạn bè
    $query = "DELETE FROM friendships WHERE
  (user_id = $current_user_id AND friend_id = $friend_id) OR (user_id = $friend_id AND friend_id = $current_user_id)
  AND status = 'accepted'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

// Chức năng chấp nhận lời mời kết bạn từ người khác
if (isset($_POST['accept_friend_request'])) {
    $current_user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];
    $query = "UPDATE friendships SET status = 'accepted' WHERE user_id = $friend_id AND friend_id = $current_user_id AND status = 'pending'";

    sendNotiToUser($friend_id, $current_user_id, 'acceptfriend', $conn);
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

// Chức năng từ chối lời mời kết bạn từ người khác
if (isset($_POST['decline_friend_request'])) {
    $current_user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];
    $query = "DELETE FROM friendships WHERE user_id = $friend_id AND friend_id = $current_user_id AND status = 'pending'";
    sendNotiToUser($current_user_id, $friend_id, 'declinefriend', $conn);
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

// Chức năng chuyển đến trang nhắn tin
if (isset($_POST['openMessagePage'])) {
    $current_user_id = $_SESSION['user_id'];
    $profile_user_id = $_POST['profileUserId'];

    $conversation_id = $current_user_id > $profile_user_id
        ? "$profile_user_id" . "_" . "$current_user_id"
        : "$current_user_id" . "_" . "$profile_user_id";

    // Kiểm tra xem cuộc trò chuyện đã tồn tại chưa
    $checkQuery = "SELECT conversation_id FROM conversations WHERE conversation_id = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "s", $conversation_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        // Nếu chưa có, tạo mới cuộc trò chuyện
        $insertQuery = "INSERT INTO conversations (conversation_id, user1_id, user2_id, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "sii", $conversation_id, $current_user_id, $profile_user_id);
        mysqli_stmt_execute($stmt);
    }

    // Trả về conversation_id để FE chuyển hướng
    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation_id
    ]);
}

// Hàm gửi thông báo cho người dùng
function sendNotiToUser($senderId, $receiverId, $type, $conn)
{
    $notiQuery = '';
    switch ($type) {
        case 'addfriend':
            $notiQuery = "INSERT INTO notifications
      (user_id, sender_id, notification_type,content,reference_id) VALUES ($receiverId, $senderId, 'friend_request','đã gửi lời mời kết bạn',$senderId)";
            break;
        case 'acceptfriend':
            $notiQuery = "INSERT INTO notifications
      (user_id, sender_id, notification_type, content,reference_id) VALUES ($receiverId, $senderId, 'friend_request_accepted','đã xác nhận lời mời kết bạn',$senderId)";
            break;
        case 'declinefriend':
            $notiQuery = "INSERT INTO notifications 
      (user_id, sender_id, notification_type, content,reference_id) VALUES ($receiverId, $senderId, 'friend_request_rejected','đã từ chối lời mời kết bạn',$senderId)";
            break;
        default:
            break;
    }

    mysqli_query($conn, $notiQuery);
}
