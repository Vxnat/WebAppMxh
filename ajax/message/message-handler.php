<?php
require_once '../db_connection.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();

$_SESSION['user_id'] = 1; // ID người dung hien tai
$_SESSION['avatar'] = 'https://kenh14cdn.com/203336854389633024/2024/9/4/luu-diec-phi-o-tuoi-37-5788-1725417084281-1725417084597654952264.jpg';

// Chức năng lấy danh sách các cuộc trò chuyện trực tiếp
if (isset($_POST['getPersonChatList'])) {
    $current_user_id = $_SESSION['user_id'];// Id của người dùng hiện tại

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

            // Xác định tên của người gửi tin nhắn cuối cùng
            $last_sender_name = ($row['last_message_sender_id'] == $row['user1_id']) ? $row['user1_name'] : $row['user2_name'];

            // Lấy tin nhắn gần đây nhất
            $last_message_display = '';
            if($row['last_message'] != ''){
                if ($row['last_message_sender_id'] == $current_user_id) {
                    $last_message_display = "Bạn: " . $row['last_message'];
                } else {
                    $last_message_display = $last_sender_name . ": " . $row['last_message'];
                }
            }else{
                $last_message_display = "Chưa có tin nhắn nào";
            }
            
            $last_message_time = formatLastMessageTime($row['last_message_time']);

            // Tạo HTML cho từng cuộc trò chuyện
            $response .= '
                <li>
                    <a href="#" data-conversation="#' . $conversation_id . '">
                        <img class="content-message-image" src="' . $partner_avatar . '" alt="">
                        <span class="content-message-info">
                            <span class="content-message-name">' . $partner_name . '</span>
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

    // Trả về danh sách các cuộc trò chuyện dưới dạng HTML
    echo $response;
}

// Chức năng lấy danh sách các cuộc trò chuyện nhóm
if (isset($_POST['getGroupChatList'])) {
    $current_user_id = $_SESSION['user_id'];

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
            $last_message_display = $row['last_message'] ? $last_message_sender_name . ": " . $row['last_message'] : "Chưa có tin nhắn nào";

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

// Chức năng lấy thông tin về cuộc trò chuyện mà người dùng chọn (avatar , name ...)
if (isset($_POST['getConversationContent'])) {
    $conversation_id = $_POST['conversationId'];
    $isGroupChat = isset($_POST['isGroupChat']);

    if ($isGroupChat) {
        $chatInfo = getGroupChatById($conversation_id, $conn);
    } else {
        $chatInfo = getPersonChatById($conversation_id, $conn);
    }

    // Xử lý thông tin người dùng hoặc nhóm
    $data = [
        'currentUserId' => $chatInfo['id'],
        'name' => $chatInfo['name'],
        'avatar' => $chatInfo['avatar'],
        'lastLogin' => $isGroupChat ? 'groupChat' : $chatInfo['lastLogin'],
        'isGroupChat' => $isGroupChat,
    ];

    echo json_encode([
        'conversationId' => $conversation_id,
        'chatInfo' => $data
    ]);
}

// Chức năng lấy thông tin Chat cá nhân
function getPersonChatById($conversation_id, $conn)
{
    $current_user_id = $_SESSION['user_id'];

    $query = "
        SELECT u.user_id , u.full_name AS name, u.avatar , u.last_login
        FROM Conversations c
        JOIN Users u ON (u.user_id = c.user1_id OR u.user_id = c.user2_id)
        WHERE c.conversation_id = ? AND u.user_id != ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $conversation_id, $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return [
            'id' => $current_user_id,
            'name' => $row['name'],
            'avatar' => $row['avatar'] ? $row['avatar'] : '../img/default-avatar.png',
            'lastLogin' => $row['last_login']
        ];
    }
    return ['name' => 'Unknown User', 'avatar' => '../img/default-avatar.png'];
}

// Chức năng lấy thông tin Chat nhóm
function getGroupChatById($group_id, $conn)
{
    $current_user_id = $_SESSION['user_id'];
    
    $query = "SELECT group_name AS name, group_avatar AS avatar FROM GroupChats WHERE group_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $group_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return [
            'id' => $current_user_id,
            'name' => $row['name'],
            'avatar' => $row['avatar'] ? $row['avatar'] : '../img/default-group.png',
        ];
    }
    return ['name' => 'Unknown Group', 'avatar' => '../img/default-group.png'];
}

//  Chức năng cập nhật last message
if(isset($_POST['updateLastMessage'])){
    $current_user_id = $_SESSION['user_id'];
    $conversation_id = $_POST['conversationId'];
    $last_message = $_POST['message'] != null ? $_POST['message'] : null; // Đảm bảo null nếu không có tin nhắn
    $last_message_sender_id = $_POST['senderId'] != null ? $_POST['senderId'] : null; // Đảm bảo null nếu không có tin nhắn
    $isGroupChat = isset($_POST['isGroupChat']) ? true : false;

    $lastSenderNameQuery = '';

    if($isGroupChat){
        $query = "UPDATE GroupChats SET last_message =?, last_message_sender_id =? , last_message_time = NOW() WHERE group_id =?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sii", $last_message, $last_message_sender_id, $conversation_id);
        mysqli_stmt_execute($stmt);

        // Nếu còn tin nhắn, lấy tên người gửi cuối cùng
        if ($last_message_sender_id !== null) {
            $lastSenderNameQuery = "SELECT u.user_id, u.full_name as last_sender_name FROM GroupChats gc
                JOIN users u ON gc.last_message_sender_id = u.user_id
                WHERE gc.group_id = ? AND u.user_id = ?";
        }
        
    }else{
        $query = "UPDATE Conversations SET last_message =?, last_message_sender_id =? , last_message_time = NOW() WHERE conversation_id =?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sis", $last_message, $last_message_sender_id, $conversation_id);
        mysqli_stmt_execute($stmt);

        // Nếu còn tin nhắn, lấy tên người gửi cuối cùng
        if ($last_message_sender_id !== null) {
            $lastSenderNameQuery = "SELECT u.user_id, u.full_name as last_sender_name FROM Conversations c
                JOIN users u ON c.last_message_sender_id = u.user_id
                WHERE c.conversation_id = ? AND u.user_id = ?";
        }
        
    }

    // Chỉ thực hiện truy vấn tên người gửi nếu còn tin nhắn
    if ($last_message_sender_id !== null && !empty($lastSenderNameQuery)) {
        $stmt = mysqli_prepare($conn, $lastSenderNameQuery);
        mysqli_stmt_bind_param($stmt, "ii", $conversation_id, $last_message_sender_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            // Nếu tin nhắn cuối là bản thân
            if ($row['user_id'] == $current_user_id) {
                echo json_encode(['success' => true, 'last_sender_name' => 'Bạn']);
            } else {
                // Nếu tin nhắn cuối là người khác
                echo json_encode(['success' => true, 'last_sender_name' => $row['last_sender_name']]);
            }
        } else {
            // Trường hợp không tìm thấy người gửi
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy người gửi']);
        }
    } else {
        // Trường hợp không còn tin nhắn
        echo json_encode(['success' => true, 'last_sender_name' => null]);
    }

    mysqli_stmt_close($stmt);
    exit();
}

// Chức năng lấy thông tin về nhóm chat
if(isset($_POST['getGroupChatInfo'])){
    $current_user_id = $_SESSION['user_id'];
    $group_id = $_POST['groupId'];
    $query = "SELECT * FROM GroupChats WHERE group_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $group_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    echo json_encode(['success' => true, 'group_info' => $result->fetch_assoc() , 'current_user_id' => $current_user_id]);
}

// Chức năng thay đổi thông tin nhóm
if(isset($_POST['updateGroupChatInfo'])){
    $group_id = $_POST['groupId'];
    $group_name = $_POST['newName'];
    $group_des = $_POST['newDescription'] ?? null;
    $group_avatar = $_POST['newAvatar'] ?? null;

    // Xây dựng query động
    $query = "UPDATE GroupChats SET group_name = ?, description = ?";
    $params = [$group_name, $group_des];
    $types = "ss";

    if ($group_avatar) {
        $query .= ", group_avatar = ?";
        $params[] = $group_avatar;
        $types .= "s";
    }

    $query .= " WHERE group_id = ?";
    $params[] = $group_id;
    $types .= "i";

    // Chuẩn bị và thực thi truy vấn
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true]);
}

// Chức năng lấy về thông tin các thành viên trong nhóm chat
if(isset($_POST['getMemberGroup'])){
    $group_id = $_POST['groupId'];
    
    $query = "SELECT u.user_id, u.full_name, u.avatar FROM GroupMembers gm
              JOIN Users u ON gm.user_id = u.user_id
              WHERE gm.group_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $group_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $members = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
    }

    // Kiểm tra nếu không có thành viên nào, trả về một mảng rỗng
    echo json_encode($members);
}

// Chức năng lấy danh sách bạn để chuẩn bị thêm vào nhóm chat
if(isset($_POST['addMemberGroup'])){
    $group_id = $_POST['groupId'];
    // $user_id = $_SESSION['user_id'];
    $user_id = 1;

    $friendshipQuery = "SELECT u.user_id, u.full_name, u.avatar FROM Users u
    JOIN (
        SELECT user_id AS friend_id FROM Friendships WHERE friend_id = ? AND status = 'accepted'
        UNION ALL
        SELECT friend_id AS friend_id FROM Friendships WHERE user_id = ? AND status = 'accepted'
    ) f     ON u.user_id = f.friend_id
    WHERE NOT EXISTS (
        SELECT 1 FROM groupmembers gm WHERE gm.user_id = u.user_id AND gm.group_id = ?
    );";
            
    $friendshipStmt = mysqli_prepare($conn, $friendshipQuery);
    mysqli_stmt_bind_param($friendshipStmt, "iii", $user_id, $user_id, $group_id);
    mysqli_stmt_execute($friendshipStmt);
    $result = mysqli_stmt_get_result($friendshipStmt);

    // Lưu danh sách bạn bè vào mảng
    $friends = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $friends[] = [
            'user_id' => $row['user_id'],
            'full_name' => $row['full_name'],
            'avatar' => $row['avatar'] ? $row['avatar'] : '../img/default-avatar.png'
        ];
    }

    // In danh sách bạn bè
    echo json_encode($friends, JSON_PRETTY_PRINT);
}

// Chức năng thêm vào nhóm chat
if(isset($_POST['addMemberToGroup'])){
    $group_id = $_POST['groupId'];
    $user_id = $_POST['userId'];

    $query = "INSERT INTO GroupMembers (group_id, user_id) VALUES (?,?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $group_id, $user_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true]);
}

// Chức năng lấy danh sách thành viên để chuẩn bị xóa khỏi nhóm chat
if(isset($_POST['removeMemberGroup'])){
    $group_id = $_POST['groupId'];
    
    $query = "SELECT u.user_id, u.full_name, u.avatar 
    FROM GroupMembers gm
    JOIN groupchats gc ON gc.group_id = gm.group_id
    JOIN Users u ON gm.user_id = u.user_id
    WHERE gm.group_id = ?
    AND gm.user_id != gc.admin_id;";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $group_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $members = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
    }

    // Kiểm tra nếu không có thành viên nào, trả về một mảng rỗng
    echo json_encode($members);
}

// Chức năng xóa khỏi nhóm chat
if(isset($_POST['removeMemberToGroup'])){
    $group_id = $_POST['groupId'];
    $user_id = $_POST['userId'];

    $query = "DELETE FROM groupmembers WHERE group_id = ? AND user_id = ?;";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $group_id, $user_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true]);
}

// Chức năng tạo thêm nhóm chat
if(isset($_POST['createGroupChat'])){
    $current_user_id = $_SESSION['user_id'];

    $group_name = $_POST['newName'];
    $group_des = $_POST['newDescription'] ?? null;
    $group_avatar = $_POST['newAvatar'] ?? null;

    // Xây dựng query động
    $query = "INSERT INTO GroupChats (admin_id,group_name, description";
    $params = [$current_user_id,$group_name, $group_des];
    $types = "sss";

    if ($group_avatar) {
        $query .= ", group_avatar) VALUES (?,?,?,?)";
        $params[] = $group_avatar;
        $types .= "s";
    }else{
        $query .= ") VALUES (?,?,?)";
    }

    // Chuẩn bị và thực thi truy vấn
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $group_id = mysqli_insert_id($conn);

    if(mysqli_stmt_affected_rows($stmt) > 0){
        $memberQuery = "INSERT INTO GroupMembers (group_id, user_id) VALUES (?,?)";
        $memberStmt = mysqli_prepare($conn, $memberQuery);
        mysqli_stmt_bind_param($memberStmt, "ii", $group_id, $current_user_id);
        mysqli_stmt_execute($memberStmt);
    }

    echo json_encode(['success' => true]);
}

// Chức năng xóa nhóm chat
if(isset($_POST['deleteGroupChat'])){
    $group_id = $_POST['groupId'];

    $query = "DELETE FROM GroupChats WHERE group_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $group_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true]);    
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

?>