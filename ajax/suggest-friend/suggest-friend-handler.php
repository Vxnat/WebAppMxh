<?php 
    require_once '../db_connection.php';

    // Lấy danh sách các gợi ý kết bạn
    if(isset($_POST['getSuggestionsList'])){
        // $userId = $_SESSION['user_id'];
        $userId = 1;
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
        $stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
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
        $userId = 1;
        // $userId = $_SESSION['user_id'];
        $receiverId = $_POST['receiverId'];

        // Kiểm tra xem đã kết bạn chưa
        $checkFriendQuery = "SELECT * FROM Friendships WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
        $stmt = $conn->prepare($checkFriendQuery);
        $stmt->bind_param("iiii", $userId, $receiverId, $receiverId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            echo json_encode(['success' => false,'message' => 'Bạn đã gửi kết bạn với người này rồi']);
            exit();
        }

        // Thêm mối quan hệ và thông báo kết bạn
        $insertQuery = "INSERT INTO Friendships (user_id, friend_id,status) VALUES ($userId, $receiverId,'pending')";

        if($conn->query($insertQuery)){
            $Notiquery = "INSERT INTO Notifications (user_id, sender_id, notification_type,content,reference_id) VALUES ($receiverId,$userId,'friend_request','đã gửi lời mời kết bạn',$userId)";
            $conn->query($Notiquery);

            echo json_encode(['success' => true,'message' => 'Đã gửi kết bạn thành công']);
        }else{
            echo json_encode(['success' => false,'message' => 'Có lỗi xảy ra']);
        }

    }

    // Lấy về danh sách những người đã gửi kết bạn cho người dùng 
    if(isset($_POST['getFriendRequests'])){
        // $userId = $_SESSION['user_id'];
        $userId = 1;
        $query = "SELECT u.user_id, u.full_name, u.avatar
        FROM Users u
        JOIN Friendships f ON u.user_id = f.user_id
        WHERE f.friend_id = ? AND f.status = 'pending';";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $html = '';
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
                $name = $row['full_name'];
                $profileUrl = "profile.php?user_id=" . $row['user_id'];
                $html .= '<li class="friend-request" data-request-id="' . $row['user_id'] . '">
                        <div class="friend-request-info">
                            <div class="friend-request-avatar">
                                <a href="'.$profileUrl.'">
                                    <img src="'.$avatar.'" alt="avatar">
                                </a>
                            </div>
                            <a href="'.$profileUrl.'">'.$name.'</a>
                        </div>
                        <div class="friend-request-actions">
                            <button class="accept">Đồng ý</button>
                            <button class="decline">Từ chối</button>
                        </div>
                    </li>';
            }
        }else{
            $html.= '<p class="no-request">Chưa có lời mời kết bạn</p>';
        }

        echo json_encode(['success' => true, 'html' => $html]);
    }

    // Đồng ý lời mời kết bạn
    if(isset($_POST['acceptFriendRequest'])){
        $userId = 1;
        // $userId = $_SESSION['user_id'];
        $requestId = $_POST['requestId'];

        $updateQuery = "UPDATE Friendships SET status = 'accepted' WHERE user_id = ? AND friend_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ii", $requestId, $userId);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            $Notiquery = "INSERT INTO Notifications (user_id, sender_id, notification_type, content, reference_id) VALUES ($requestId,$userId,'friend_request_accepted','đã xác nhận kết bạn',$userId)";
            $conn->query($Notiquery);

            echo json_encode(['success' => true,'message' => 'Đã xác nhận lời mời kết bạn']);
        }
        
    }

    // Từ chối lời mời kết bạn
    if(isset($_POST['declineFriendRequest'])){
        $userId = 1;
        // $userId = $_SESSION['user_id'];
        $requestId = $_POST['requestId'];

        $updateQuery = "DELETE FROM Friendships WHERE user_id = ? AND friend_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ii", $requestId, $userId);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            $Notiquery = "INSERT INTO Notifications (user_id, sender_id, notification_type, content, reference_id) VALUES ($requestId,$userId,'friend_request_rejected','đã từ chối lời mời kết bạn',$userId)";
            $conn->query($Notiquery);

            echo json_encode(['success' => true,'message' => 'Đã từ chối lời mời kết bạn']);
        }
    }
    