<?php 
    require_once '../ajax_action.php';

    if(isset($_POST['fetchSuggest'])){
        $user_id = $_SESSION['user_id'];

        $query = "SELECT user_id, full_name , avatar 
        FROM users WHERE created_at >= NOW() - INTERVAL 30 DAY
        AND user_id != ? -- Khong lay chinh ban than
        AND user_id NOT IN (
        SELECT friend_id FROM friendships WHERE user_id = ?
        UNION
        SELECT user_id FROM friendships WHERE friend_id = ?
        ) -- Khong lay nhung nguoi minh da ket ban
        LIMIT 5;";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $user_id,$user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '<div class="sidebar__wrapper-content">
        <ul class="sidebar__wrapper-list">
        ';

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $userId = $row['user_id'];
                $avatar = $row['avatar'] != '' ? $row['avatar'] : '../img/default-avatar.png';
                $fullName = $row['full_name'];
                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
                $output .= "
                <li class='sidebar__wrapper-item' style='position:relative;' data-user-id='$userId'>
                                    <a href='$profileUrl'><img src='$avatar'
                                        alt='' /></a>
                                    <a href='$profileUrl'><div  class='sidebar__wrapper-item_content'>
                                        <span class='name'>$fullName</span>
                                        <div class='subsciption'>Suggested for you</div>
                                    </div>
                                    </a>
                                    <button type='button' class='toggle-add-friend'><i style='color:#1877f2' class=\"fa-solid fa-user-plus\"></i></button>
                            </li>
                            ";
            }
        }

        $output .= '</ul></div>';

        echo $output;

    }

    // Người dùng hiện tại gửi lời mời kết bạn với người khác
    if(isset($_POST['sendFriendRequest'])){
        $user_id = $_SESSION['user_id']; // Id nguoi gửi
        $receiverId = $_POST['receiverId']; // Id người nhận

        // Kiểm tra nếu đã gửi lời mời trước đó hoặc đã là bạn bè
        $checkQuery = "SELECT * FROM friendships 
        WHERE (user_id = $user_id AND friend_id = $receiverId) 
        OR (user_id = $receiverId AND friend_id = $user_id)";

        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            echo "Lời mời đã tồn tại hoặc hai người đã là bạn bè.";
            exit();
        }

        // Thêm lời mời kết bạn vào bảng friendships
        $insertQuery = "INSERT INTO friendships (user_id, friend_id, status) 
        VALUES ($user_id, $receiverId, 'pending')";

        if ($conn->query($insertQuery)) {
            // Thêm thông báo cho người nhận
            $notiQuery = "INSERT INTO notifications (user_id, sender_id, notification_type,content,reference_id) 
                VALUES ($receiverId, $user_id, 'friend_request','đã gửi lời mời kết bạn',$user_id)";
            $conn->query($notiQuery);

        echo "success";
        } else {
            echo "Lỗi khi gửi lời mời kết bạn: " . $conn->error;
        }
    }

    // Người dùng hiện tại xóa lời mời kết bạn với người khác
    if (isset($_POST['cancelFriendRequest'])) {
        $user_id = $_SESSION['user_id'];
        $receiver_id = $_POST['receiverId'];
    
        // Xóa lời mời kết bạn
        $deleteQuery = "DELETE FROM friendships WHERE user_id = $user_id AND friend_id = $receiver_id AND status = 'pending'";
        
        if ($conn->query($deleteQuery)) {
            // Xóa thông báo
            $deleteNoti = "DELETE FROM notifications WHERE user_id = $receiver_id AND sender_id = $user_id AND notification_type = 'friend_request'";
            $conn->query($deleteNoti);
    
            echo "success";
        } else {
            echo "Lỗi khi hủy lời mời: " . $conn->error;
        }
    }
    