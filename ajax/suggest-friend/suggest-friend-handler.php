<?php 
    require_once '../db_connection.php';

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
