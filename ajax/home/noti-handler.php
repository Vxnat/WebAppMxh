<?php
    require_once '../ajax_action.php';

    // Fetch Notifications
    if (isset($_POST["fetchNoti"])) { 
        // Chuẩn bị truy vấn
        $userId = $_SESSION['user_id'];
        $query = "SELECT n.*, u.full_name AS sender_name, u.avatar AS sender_avatar 
                  FROM notifications n
                  LEFT JOIN users u ON n.sender_id = u.user_id
                  WHERE n.user_id = ?
                  ORDER BY n.created_at DESC LIMIT 5";
        
        // Sử dụng prepared statement để bảo mật
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $output = '';
        $noti_ids = []; // Mảng lưu ID các thông báo
        
        if ($result->num_rows > 0) {
            $output .= '<ul class="header__notify-list">';
            while ($row = $result->fetch_assoc()) {
                $output .= generateNotis($row);
                // Chỉ thêm vào danh sách cập nhật nếu thông báo chưa được đọc
                if ($row['is_read'] == 0) {
                    $noti_ids[] = $row['notification_id'];
                }
            }
            $output .= "</ul>";
            $output .= '<div class="header__notify-footer" id="more-noti-btn">See previous notifications</div>';

            // Sau khi lấy xong, cập nhật trạng thái `is_read = 1`
            markNotificationsAsRead($conn,$noti_ids);
        }else{
            $output .= "<p style='text-align:center;'>Không có thông báo!</p>";
        }
        echo $output;
    }

    // Show More Notifications
    if(isset($_POST['moreNoti'])){
        $userId = $_SESSION['user_id']; 
        $lastCreatedAt = $_POST['lastCreatedAt'];

        $query = "SELECT n.*, u.full_name AS sender_name, u.avatar AS sender_avatar 
                FROM notifications n
                LEFT JOIN users u ON n.sender_id = u.user_id
                WHERE n.user_id = ? AND n.created_at < ?
                ORDER BY n.created_at DESC LIMIT 10";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $userId,$lastCreatedAt);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '';
        $noti_ids = []; // Mảng lưu ID các thông báo

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $output .= generateNotis($row);
                // Chỉ thêm vào danh sách cập nhật nếu thông báo chưa được đọc
                if ($row['is_read'] == 0) {
                    $noti_ids[] = $row['notification_id'];
                }
            }
            // Sau khi lấy xong, cập nhật trạng thái `is_read = 1`
            markNotificationsAsRead($conn,$noti_ids);
        } else {
            echo 'failed';
        }
    
        echo $output;

    }

    // Generate Notifications
    function generateNotis($row){
        
        // Sử dụng dữ liệu từ `sender_name` và `sender_avatar`
        $sender_name = htmlspecialchars($row['sender_name']);
        $sender_avatar = !empty($row['sender_avatar']) ? $row['sender_avatar'] : '../img/default-avatar.png';
        $time_ago = formatTime($row['created_at']); 
        $content = $row['content']; 
        $noti_id = $row['notification_id'];
        $sender_id = $row['sender_id'];
        $reference_id = $row['reference_id'];
        $is_read = $row['is_read'];

        $icon_map = [
            'friend_request' => '../img/friend-request.png',
            'friend_request_accepted' => '../img/friend-accepted.png',
            'friend_request_rejected' => '../img/friend-rejected.png',
            'comment' => '../img/comment.png',
            'like' => '../img/like.png',
            'share' => '../img/share.png',
        ];

        $noti_type = $row['notification_type'];
        $type_icon = isset($icon_map[$noti_type]) ? $icon_map[$noti_type] : '../img/noti-default.png';

        // **Tạo link dựa trên reference_id**
        $link = '#';
        if ($noti_type == 'like' || $noti_type == 'comment' || $noti_type == 'share') {
        $link = "../page/post.php?id=$reference_id"; // Chuyển đến bài viết
        } elseif ($noti_type == 'friend_request' || $noti_type == 'friend_request_accepted' || $noti_type == 'friend_request_rejected') {
            $link = "../page/profile.php?id=$reference_id"; // Chuyển đến trang cá nhân
        }
        
        // Bọc `<a>` cho tất cả thông báo, bao gồm cả friend_request
        $output = "<a href=\"$link\" class=\"header__notify-link\">";

        $output .= "<li class=\"header__notify-item\" data-notification-id=\"$noti_id\" data-sender-id=$sender_id data-created-at=\"{$row['created_at']}\">";

        $output .= "
                    <div class=\"header__notify-img\">
                        <img src=\"$sender_avatar\" alt=\"\" class='avatar'  />
                        <div class='type'>
                            <img src='$type_icon' alt='$noti_type' />
                        </div>
                    </div>
                    <div class=\"header__notify-info\">
                    <span class=\"header__notify-name\">$sender_name</span>";

        switch ($row['notification_type']) {
            case 'friend_request':
                $output .= "<span class=\"header__notify-des\"> $content</span>";
                break;
            case 'friend_request_accepted':
                $output .= "<span class=\"header__notify-des\"> $content</span>";
                break;
            case 'friend_request_rejected':
                $output .= "<span class=\"header__notify-des\"> $content</span>";
                break;
            case "like":
                $output .= "<span class=\"header__notify-des\"> $content</span>";
                break;
            case "comment":
                $output .= "<span class=\"header__notify-des\"> $content</span>";
                break;
            case "share":
                $output .= "<span class=\"header__notify-des\"> $content</span>";
                break;
            // Thêm các case khác nếu cần
            default:
                break;
        }

        // Đóng thẻ `<a>` trước khi thêm nút bấm

        $output .= "<div class=\"header__notify-date\">$time_ago</div>
            </div>";
        
        if($is_read == 0) $output .= "<div class='unread_noti'></div>";

        $output .= "</a>";

        // Nếu là `friend_request`, thêm nút xác nhận và hủy
        if ($noti_type === 'friend_request') {
            $output .= "
            <div class=\"header__notify-actions\">
                    <button type=\"button\" class=\"accept\">Xác nhận</button>
                    <button type=\"button\" class=\"reject\">Hủy</button>
            </div>";
        }
        
        $output .= "</li>";
        return $output;
    }

    // Check New Noti
    if (isset($_POST['checkNewNoti'])) {
        $userId = $_SESSION['user_id'];
    
        $query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if ($row['unread_count'] > 0) {
            echo json_encode([
                'success'=> true,
                'unread_count'=> $row['unread_count'],
            ]);
        } else {
            echo json_encode([
                'success'=> false,
            ]);
        }
    }        

    // Mark Noti As Read
    function markNotificationsAsRead($conn, $noti_ids) {
        if (!empty($noti_ids)) {
            $noti_ids_str = implode(",", $noti_ids); // Chuyển mảng thành chuỗi ID
            $update_query = "UPDATE notifications SET is_read = 1 WHERE notification_id IN ($noti_ids_str)";
            mysqli_query($conn, $update_query);
        }
    }

    // Người dùng hiện tại chấp nhận lời mời kết bạn của người khác
    if(isset($_POST["acceptFriendRequest"])){
        $user_id = $_SESSION['user_id']; // ID người dùng hiện tại
        $sender_id = $_POST['sender_id']; // ID người gửi kết bạn
        $notification_id = $_POST['notification_id']; // ID thông báo

        // Người dùng hiện tại chấp nhận lời mời kết bạn của người khác
        $query = "UPDATE friendships 
        SET status = 'accepted', updated_at = NOW() , accepted_at = NOW()
        WHERE user_id = $sender_id AND friend_id = $user_id  AND status = 'pending'";

        $result = $conn->query($query);

        if ($conn->affected_rows > 0) {
            // Xóa thông báo lời mời kết bạn
            $deleteQuery = "DELETE FROM notifications WHERE notification_id = $notification_id";
            $conn->query($deleteQuery);

            // Gửi thông báo chấp nhận kết bạn
            $notiQuery = "INSERT INTO notifications(user_id, sender_id, notification_type,content,reference_id)
            VALUES ($sender_id,$user_id,'friend_request_accepted','đã xác nhận lời mời kết bạn',$user_id)";

            $conn->query($notiQuery);

            echo "success";
        }else{
            echo "Không tìm thấy lời mời kết bạn hoặc đã được chấp nhận trước đó.";
        }
    }

    // Người dùng hiện tại từ chối lời mời kết bạn của người khác
    if(isset($_POST["rejectFriendRequest"])){
        $user_id = $_SESSION['user_id']; // ID người dùng hiện tại
        $sender_id = $_POST['sender_id']; // ID người gửi kết bạn
        $notification_id = $_POST['notification_id']; // ID thông báo

        // Từ chối lời mời kết bạn
        $deleteFriendshipQuery = "DELETE FROM friendships
        WHERE user_id = $sender_id AND friend_id = $user_id AND status = 'pending'";

        $result = $conn->query($deleteFriendshipQuery);

        if ($conn->affected_rows > 0) {
            // Xóa thông báo lời mời kết bạn
            $deleteNoti = "DELETE FROM notifications WHERE notification_id = $notification_id";
            $conn->query($deleteNoti);

            // Gửi thông báo từ chối kết bạn
            $notiQuery = "INSERT INTO notifications(user_id,sender_id,notification_type,content,reference_id)
            VALUES ($sender_id,$user_id,'friend_request_rejected','đã từ chối lời mời kết bạn',$user_id)";

            $conn->query($notiQuery);

            echo "success";
        }else{
            echo "failed";
        }
    }