<?php
require_once '../db_connection.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (isset($_POST['getPersonChatList'])) {
    // $user_id = $_SESSION['user_id']; // Id của người dùng hiện tại

    $current_user_id = 1;

    // Truy vấn để lấy danh sách các cuộc trò chuyện của người dùng
    $query = "
        SELECT c.conversation_id, 
               u1.user_id AS user1_id, u1.full_name AS user1_name, u1.avatar AS user1_avatar,
               u2.user_id AS user2_id, u2.full_name AS user2_name, u2.avatar AS user2_avatar,
               c.last_message, c.last_message_sender_id, c.last_message_time
        FROM Conversations c
        LEFT JOIN Users u1 ON u1.user_id = c.user1_id
        LEFT JOIN Users u2 ON u2.user_id = c.user2_id
        WHERE c.user1_id = ? OR c.user2_id = ?";

    // Chuẩn bị truy vấn
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $current_user_id, $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Lấy danh sách các cuộc trò chuyện và trả về dưới dạng HTML
    $response = '';
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Lấy thông tin cuộc trò chuyện (đối tượng người dùng)
            $conversation_id = $row['conversation_id'];
            $user1_name = $row['user1_name'];
            $user1_avatar = $row['user1_avatar'] ? $row['user1_avatar'] : '../img/default-avatar.png';
            $user2_name = $row['user2_name'];
            $user2_avatar = $row['user2_avatar'] ? $row['user2_avatar'] : '../img/default-avatar.png';

            // Xác định người dùng hiện tại (người đăng nhập) và người còn lại
            if ($row['user1_id'] == $current_user_id) {
                $partner_name = $user2_name;
                $partner_avatar = $user2_avatar;
            } else {
                $partner_name = $user1_name;
                $partner_avatar = $user1_avatar;
            }

            // Lấy tin nhắn gần đây nhất
            $last_message_display = '';
            if ($row['last_message_sender_id'] == $current_user_id) {
                $last_message_display = "Bạn: " . $row['last_message'];
            } else {
                $last_message_display = $row['last_message'];
            }

            $last_message_time = formatLastMessageTime($row['last_message_time']);

            // Tạo HTML cho từng cuộc trò chuyện
            $response .= '
                <li>
                    <a href="#" data-conversation="#' . $conversation_id . '">
                        <img class="content-message-image" src="' . $partner_avatar . '" alt="">
                        <span class="content-message-info">
                            <span class="content-message-name">' . $partner_name . '</span>
                            <span class="content-message-text">' . $last_message_display . '</span>  <!-- Giả lập tin nhắn gần đây -->
                        </span>
                        <span class="content-message-more">
                            <div class="content-message-time">' . $last_message_time . '</div>
                        </span>
                    </a>
                </li>
            ';
        }
    }

    // Trả về danh sách các cuộc trò chuyện dưới dạng HTML
    echo $response;
}

// Chức năng lấy danh sách các cuộc trò chuyện nhóm
if (isset($_POST['getGroupChatList'])) {
    // $current_user_id = $_SESSION['user_id']; // Lấy user_id từ session
    $current_user_id = 1;

    // Truy vấn lấy danh sách group chat mà user tham gia
    $query = "
        SELECT g.group_id, g.group_name, g.group_avatar, g.last_message, g.last_message_sender_id, g.last_message_time,
               u.full_name AS last_sender_name
        FROM GroupChats g
        JOIN GroupMembers gm ON g.group_id = gm.group_id
        LEFT JOIN Users u ON g.last_message_sender_id = u.user_id
        WHERE gm.user_id = ?
        ORDER BY g.last_message_time DESC"; // Sắp xếp theo tin nhắn mới nhất

    // Chuẩn bị truy vấn
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Xử lý kết quả và tạo HTML
    $response = '';
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $group_id = $row['group_id'];
            $group_name = htmlspecialchars($row['group_name']);
            $group_avatar = $row['group_avatar'] ? $row['group_avatar'] : '../img/default-group.png';

            // Hiển thị tin nhắn cuối cùng
            $last_message_sender_name = $row['last_message_sender_id'] == $current_user_id ? "Bạn" : $row['last_sender_name'];
            $last_message_display = $row['last_message'] ? $last_message_sender_name . ": " . $row['last_message'] : "No messages yet";

            // Định dạng thời gian tin nhắn cuối
            $last_message_time = $row['last_message_time'] ? formatLastMessageTime($row['last_message_time']) : '';

            // Tạo HTML cho từng group chat
            $response .= '
                <li>
                    <a href="#" data-conversation="#' . $group_id . '" data-group-chat="true">
                        <img class="content-message-image" src="' . $group_avatar . '" alt="">
                        <span class="content-message-info">
                            <span class="content-message-name">' . $group_name . '</span>
                            <span class="content-message-text">' . $last_message_display . '</span>
                        </span>
                        <span class="content-message-more">
                            <div class="content-message-time">' . $last_message_time . '</div>
                        </span>
                    </a>
                </li>
            ';
        }
    }

    echo $response;
}

// Định dạng thời gian cho tin nhắn gần đây 
function formatLastMessageTime($timestamp)
{
    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return "Just now"; // Dưới 1 phút
    } elseif ($diff < 3600) {
        return floor($diff / 60) . " min ago"; // Dưới 1 giờ
    } elseif (date("Y-m-d", $time) == date("Y-m-d", $now)) {
        return "Today " . date("H:i", $time); // Cùng ngày
    } elseif (date("Y-m-d", $time) == date("Y-m-d", strtotime("-1 day"))) {
        return "Yesterday " . date("H:i", $time); // Hôm qua
    } elseif ($diff < 7 * 86400) {
        return date("D H:i", $time); // Trong tuần (hiển thị thứ, ví dụ: Mon 14:30)
    } else {
        return date("d/m/Y", $time); // Cũ hơn 7 ngày
    }
}