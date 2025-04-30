<?php
    require_once '../ajax_action.php';

    // Fetch Activity
    if (isset($_POST["fetchActivity"])) { 
        // Chuẩn bị truy vấn
        $user_id = $_SESSION['user_id'];
        
        $query = "SELECT n.*, u.full_name AS sender_name, u.avatar AS sender_avatar 
                  FROM notifications n
                  LEFT JOIN Users u ON n.sender_id = u.user_id
                  WHERE n.user_id = ?
                  ORDER BY n.created_at DESC LIMIT 5";
        
        // Sử dụng prepared statement để bảo mật
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $output = '';
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                switch ($row['notification_type']) {
                    case "comment":
                        // Sử dụng dữ liệu từ `sender_name` và `sender_avatar`
                        $sender_name = htmlspecialchars($row['sender_name']);
                        $sender_avatar = $row['sender_avatar'] != null ? $row['sender_avatar'] : '../img/default-avatar.png';
                        $time_ago = formatTime($row['created_at']); 

                        $output .= "<li class=\"sidebar__wrapper-item\">
                                    <img src=\"$sender_avatar\"
                                        alt=\"\" />
                                    <div class=\"sidebar__wrapper-item_content\">
                                        <span class=\"name\">$sender_name</span>
                                        <span class=\"text\">bình luận trong bài viết của bạn!</span>
                                        <div class=\"time\">$time_ago</div>
                                    </div>
                                </li>";
                        break;
                    case "like":
                        // Sử dụng dữ liệu từ `sender_name` và `sender_avatar`
                        $sender_name = htmlspecialchars($row['sender_name']);
                        $sender_avatar = $row['sender_avatar'] != null ? $row['sender_avatar'] : '../img/default-avatar.png';
                        $time_ago = formatTime($row['created_at']); 

                        $output .= "<li class=\"sidebar__wrapper-item\">
                                    <img src=\"$sender_avatar\"
                                        alt=\"\" />
                                    <div class=\"sidebar__wrapper-item_content\">
                                        <span class=\"name\">$sender_name</span>
                                        <span class=\"text\">thích bài viết của bạn!</span>
                                        <div class=\"time\">$time_ago</div>
                                    </div>
                                </li>";
                        break;
                    // Thêm các case khác nếu cần
                    default:
                        break;
                }
            }
        }
        echo $output;
    }