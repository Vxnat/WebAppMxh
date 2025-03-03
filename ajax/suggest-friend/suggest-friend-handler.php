<?php 
    require_once '../db_connection.php';

    if(isset($_POST['getSuggestionsList'])){
        // $currentUserId = $_SESSION['user_id'];
        $currentUserId = 1;
        // Lấy danh sách giới thiệu ngẫu nhiên
        $query = "SELECT u.user_id, u.full_name, u.avatar
            FROM Users u
            WHERE u.user_id NOT IN (
                -- Loại bỏ bạn bè đã kết bạn
                SELECT CASE 
                    WHEN f.user_id = ? THEN f.friend_id 
                    ELSE f.user_id 
                END 
                FROM Friendships f 
                WHERE f.user_id = ? OR f.friend_id = ?
            )
            AND u.user_id != ? -- Không hiển thị chính mình
            ORDER BY RAND() -- Lấy ngẫu nhiên
            LIMIT 10;
            ";      

        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiii", $currentUserId, $currentUserId, $currentUserId, $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result();

        $html = '';
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
                $html .= '<li class="suggestion" data-user-id="' . $row['user_id'] . '">
                    <a href="#"><img src="' . $avatar . '" alt="avatar"></a>
                    <div class="suggestion-details">
                        <h4>' . $row['full_name'] . '</h4>
                        <div class="action-buttons">
                            <a href="#"><button class="add">Add friend</button></a>
                            <a href="#"><button class="delete">Delete</button></a>
                        </div>
                    </div>
                </li>';
            }
        }

        echo json_encode(['success' => true, 'html' => $html]);
    }

    // Gửi lời mời kết bạn
    if(isset($_POST['addFriend'])){
        $currentUserId = 1;
        // $currentUserId = $_SESSION['user_id'];
        $receiverId = $_POST['receiverId'];

        // Kiểm tra xem đã kết bạn chưa
        $checkFriendQuery = "SELECT * FROM Friendships WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
        $stmt = $conn->prepare($checkFriendQuery);
        $stmt->bind_param("iiii", $currentUserId, $receiverId, $receiverId, $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            echo json_encode(['success' => false,'message' => 'Bạn đã gửi kết bạn với người này rồi']);
            exit();
        }

        // Thêm mối quan hệ và thông báo kết bạn
        $insertQuery = "INSERT INTO Friendships (user_id, friend_id,status) VALUES ($currentUserId, $receiverId,'pending')";

        if($conn->query($insertQuery)){
            $Notiquery = "INSERT INTO Notifications (user_id, sender_id, notification_type,content,reference_id) VALUES ($receiverId,$currentUserId,'friend_request','đã gửi lời mời kết bạn',$currentUserId)";
            $conn->query($Notiquery);

            echo json_encode(['success' => true,'message' => 'Đã gửi kết bạn thành công']);
        }else{
            echo json_encode(['success' => false,'message' => 'Có lỗi xảy ra']);
        }

    }
