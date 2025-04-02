<?php
    require_once '../ajax_action.php';

    // Like Comment
    if (isset($_POST['likeComment'])) {
        $commentId = intval($_POST['commentId']);
        $userId = $_SESSION['user_id']; 
    
        // Kiểm tra xem người dùng đã like comment này chưa
        $checkLikeQuery = "SELECT * FROM commentLikes WHERE comment_id = ? AND user_id = ?";
        $stmt = $conn->prepare($checkLikeQuery);
        $stmt->bind_param('ii', $commentId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Người dùng đã like, tiến hành xóa like
            $deleteLikeQuery = "DELETE FROM commentLikes WHERE comment_id = ? AND user_id = ?";
            $stmt = $conn->prepare($deleteLikeQuery);
            $stmt->bind_param('ii', $commentId, $userId);
            $stmt->execute();
    
            $userHasLiked = false; // Trạng thái mới sau khi xóa like
        } else {
            // Người dùng chưa like, thêm like mới
            $insertLikeQuery = "INSERT INTO commentLikes (comment_id, user_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insertLikeQuery);
            $stmt->bind_param('ii', $commentId, $userId);
            $stmt->execute();
    
            $userHasLiked = true; // Trạng thái mới sau khi thêm like
        }
    
        // Lấy tổng số lượng like hiện tại của comment
        $likeCountQuery = "SELECT COUNT(*) AS like_count FROM commentLikes WHERE comment_id = ?";
        $stmt = $conn->prepare($likeCountQuery);
        $stmt->bind_param('i', $commentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $likeCount = $result->fetch_assoc()['like_count'];
    
        // Trả kết quả về cho front-end
        echo json_encode([
            'success' => true,
            'likeCount' => $likeCount,
            'userHasLiked' => $userHasLiked,
        ]);
        exit();
    }
    
    // Send Comment
    if (isset($_POST['sendComment'])) {
        $postId = $_POST['postId']; // Lấy ID bài viết
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại
        $commentText = htmlspecialchars($_POST['commentText']); // Xử lý bình luận để tránh XSS
        $commentImg = $_POST['commentImg'];

        // Lấy id chủ sở hữu bài viết
        $queryOwner = "SELECT user_id , content FROM posts WHERE post_id = ?";
        $stmt = $conn->prepare($queryOwner);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $postData = $result->fetch_assoc();
        $postOwner = $postData['user_id']; // ID chủ bài viết
        $postContent = isset($postData['content']) ? htmlspecialchars($postData['content']) : "Bài viết này không tồn tại.";
        // Giới hạn nội dung bài viết hiển thị trong thông báo (50 ký tự)
        $postContentSnippet = mb_strimwidth($postContent, 0, 50, "...");
    
        // Thêm bình luận vào cơ sở dữ liệu
        $insertQuery = "INSERT INTO comments (post_id, user_id, comment_text,comment_img, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param('iiss', $postId, $userId, $commentText,$commentImg);
    
        if ($stmt->execute()) {
            // Lấy thông tin bình luận vừa thêm, bao gồm thông tin người dùng
            $commentId = $stmt->insert_id;
            $selectQuery = "
                SELECT 
                    c.comment_id,
                    c.comment_text,
                    c.comment_img,
                    c.created_at,
                    u.full_name,
                    u.avatar,
                    COUNT(cl.like_id) AS total_like -- Đếm số lượng like cho mỗi bình luận
                FROM 
                    comments c
                INNER JOIN 
                    users u 
                    ON u.user_id = c.user_id
                LEFT JOIN 
                    CommentLikes cl 
                    ON c.comment_id = cl.comment_id -- Kết nối với bảng likes
                WHERE 
                    c.comment_id = ? -- Điều kiện lọc theo comment_id
                GROUP BY 
                    c.comment_id, c.comment_text, c.comment_img, c.created_at, u.full_name, u.avatar;

                ";
            $selectStmt = $conn->prepare($selectQuery);
            $selectStmt->bind_param('i', $commentId);
            $selectStmt->execute();
            $result = $selectStmt->get_result();
            $commentData = $result->fetch_assoc();

            // Nếu là người dùng KHÁC -> Comment thì ko thông báo
            if($userId != $postOwner) {
                $notifQuery = "INSERT INTO Notifications (user_id, sender_id, notification_type, content, reference_id) 
                VALUES (?, ?, 'comment', ?, ?)";
                $notifStmt = $conn->prepare($notifQuery);
                $notifContent = "đã bình luận trong bài viết của bạn: \"$postContentSnippet\"";
                $notifStmt->bind_param("iisi", $postOwner, $userId, $notifContent, $postId);
                $notifStmt->execute();
            }
    
            // Trả về thông tin bình luận dưới dạng JSON
            echo json_encode([
                'success' => true,
                'commentId' => $commentData['comment_id'],
                'commentText' => htmlspecialchars($commentData['comment_text']),
                'commentImg' => $commentData['comment_img'],
                'createdAt' => formatTime($commentData['created_at']),
                'userName' => htmlspecialchars($commentData['full_name']),
                'userAvatar' => !empty($commentData['avatar']) ? $commentData['avatar'] : '../img/default-avatar.png',
                'totalLike' => $commentData['total_like']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể thêm bình luận vào cơ sở dữ liệu.']);
        }
        exit;
    }
    
    // Send Reply Comment
    if (isset($_POST['sendReply'])) {
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại
        $postId = intval($_POST['postId']); // ID bài viết
        $parentId = isset($_POST['parentId']) ? intval($_POST['parentId']) : null; // ID bình luận cha (nếu có)
        $commentText = trim($_POST['commentText']); // Nội dung bình luận
        $commentImg = trim($_POST['commentImg']); // Nội dung bình luận

        // Thêm comment vào cơ sở dữ liệu
        $insertQuery = "INSERT INTO comments (post_id, user_id, comment_text,comment_img, parent_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param('iissi', $postId, $userId, $commentText,$commentImg, $parentId);

        if ($stmt->execute()) {
            $newCommentId = $stmt->insert_id; // Lấy ID của bình luận vừa được thêm

            // Truy vấn lấy dữ liệu bình luận vừa được thêm để trả về FE
            $selectQuery = "
                SELECT 
                    c.comment_id,
                    c.comment_text,
                    c.comment_img,
                    c.created_at,
                    u.user_id,
                    u.full_name,
                    u.avatar,
                    COUNT(cl.like_id) AS total_like -- Đếm số lượng like cho mỗi bình luận
                FROM comments c
                INNER JOIN users u ON c.user_id = u.user_id
                LEFT JOIN CommentLikes cl ON c.comment_id = cl.comment_id -- Kết nối với bảng likes
                WHERE c.comment_id = ?
                GROUP BY c.comment_id, c.comment_text, c.comment_img, c.created_at, u.full_name, u.avatar;
            ";
            $stmt = $conn->prepare($selectQuery);
            $stmt->bind_param('i', $newCommentId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $commentData = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'commentId' => $commentData['comment_id'],
                    'commentText' => htmlspecialchars($commentData['comment_text']),
                    'commentImg' => $commentData['comment_img'],
                    'createdAt' => formatTime($commentData['created_at']),
                    'userName' => htmlspecialchars($commentData['full_name']),
                    'userAvatar' => !empty($commentData['avatar']) ? $commentData['avatar'] : '../img/default-avatar.png',
                    'totalLike' => $commentData['total_like'],
                ], JSON_UNESCAPED_UNICODE);
            
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to retrieve comment.']);
            }
        } else {
          echo json_encode(['success' => false, 'message' => 'Failed to add comment.']);
        }
        exit;
    }

    // Edit Comment
    if (isset($_POST['saveEditedComment'])) {
        $commentId = intval($_POST['commentId']);
        $commentText = $_POST['content'];
        $mediaUrl = isset($_POST['mediaUrl']) ? $_POST['mediaUrl'] : null;
        $isMediaDeleted = isset($_POST['deleteMedia']) ? true : false;
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại

        $query = "SELECT comment_img FROM comments WHERE comment_id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $commentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        
        $oldMediaUrl = $post["comment_img"];

        // Xác định media mới cần cập nhật
        $newMediaUrl = $oldMediaUrl; // Mặc định giữ nguyên
        if ($isMediaDeleted) {
            $newMediaUrl = ''; // Nếu xóa media
        } elseif ($mediaUrl) {
            $newMediaUrl = $mediaUrl; // Nếu có media mới
        }
    
        // Cập nhật nội dung bình luận
        $updateQuery = "UPDATE comments 
                        SET comment_text = ?,comment_img = ?, updated_at = NOW() 
                        WHERE comment_id = ? AND user_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ssii', $commentText,$newMediaUrl, $commentId, $userId);
    
        if ($stmt->execute()) {
            // Lấy thông tin chi tiết của bình luận sau khi cập nhật
            $selectQuery = "
                SELECT 
                    c.comment_img, 
                    c.created_at,
                    u.full_name, 
                    u.avatar, 
                    (SELECT COUNT(*) FROM commentlikes cl WHERE cl.comment_id = c.comment_id) AS total_like,
                    (SELECT 1 FROM commentlikes cl WHERE cl.comment_id = c.comment_id AND cl.user_id = ?) AS user_has_liked
                FROM comments c
                JOIN users u ON u.user_id = c.user_id
                WHERE c.comment_id = ?";
            
            $stmt = $conn->prepare($selectQuery);
            $stmt->bind_param('ii', $userId, $commentId); // `userId` để kiểm tra người dùng đã like hay chưa
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $commentData = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'commentData' => $commentData // Trả về dữ liệu chi tiết của bình luận
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Comment not found after update.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update the comment.'
            ]);
        }
    }

    // Delete Comment
    if (isset($_POST['deleteComment'])) {
        $commentId = intval($_POST['commentId']);
        $deleteQuery = "DELETE FROM comments WHERE comment_id = ? LIMIT 1";
        $stmt = $conn->prepare($deleteQuery);
    
        if ($stmt) {
            $stmt->bind_param("i", $commentId);
            $stmt->execute();
    
            // Kiểm tra xem có hàng nào bị ảnh hưởng không
            if ($stmt->affected_rows > 0) {
                echo 'true'; // Xóa thành công
            } else {
                echo 'false'; // Không có hàng nào bị xóa (có thể do comment_id không tồn tại)
            }
    
            $stmt->close(); // Đóng statement
        } else {
            echo 'false'; // Lỗi trong khi chuẩn bị câu lệnh
        }
    }

    // Load Reply Comment
    if (isset($_POST['loadReplies'])) {
        $user_id = $_SESSION['user_id'];
        $parentId = intval($_POST['parentId']);
        $lastCreatedAt = isset($_POST['lastCreatedAt']) ? $_POST['lastCreatedAt'] : null;
        $limit = intval($_POST['limit']);;
        $query = "SELECT c.comment_id, c.comment_text,c.comment_img, c.created_at, u.user_id , u.full_name AS username, u.avatar AS user_avatar, 
                        (SELECT COUNT(*) FROM commentlikes l WHERE l.comment_id = c.comment_id) AS like_count,
                        (SELECT COUNT(*) FROM comments c2 WHERE c2.parent_id = c.comment_id) AS has_replies,
                        EXISTS (
                            SELECT 1 
                            FROM commentlikes l2 
                            WHERE l2.comment_id = c.comment_id AND l2.user_id = ?
                        ) AS user_has_liked -- Kiểm tra người dùng hiện tại đã like comment hay chưa
                  FROM comments c
                  LEFT JOIN users u ON c.user_id = u.user_id
                  WHERE c.parent_id = ?";

        // Nếu có lastCreatedAt thì chỉ lấy bình luận cũ hơn
        if ($lastCreatedAt) {
            $query .= " AND c.created_at > ? ";
        }   

        $query .= " ORDER BY c.created_at LIMIT ?";

        $stmt = $conn->prepare($query);
        if ($lastCreatedAt) {
            $stmt->bind_param('iisi', $user_id, $parentId, $lastCreatedAt, $limit);
        } else {
            $stmt->bind_param('iii', $user_id, $parentId, $limit);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    
        $replies = [];
        while ($row = $result->fetch_assoc()) {
            $row['has_replies'] = $row['has_replies'] > 0 ? true : false; // Nếu có bình luận con, đánh dấu là true
            $replies[] = $row;
        }

        // Kiểm tra còn bình luận nào không
        $hasMore = checkMoreReplies($conn, $parentId, $lastCreatedAt);
    
        echo json_encode(['success' => true, 'replies' => $replies ,'hasMore'=> $hasMore]);
        exit;
    }

    // Load More Main Comment
    if (isset($_POST['loadMoreMainComment'])) {
        $postId = $_POST['postId'];
        $limit = intval($_POST['limit']);
        $lastCreatedAt = $_POST['lastCreatedAt'];
    
        // Xử lý HTML bình luận mới
        $html = fetchAndRenderComments($conn,$postId,$limit,$lastCreatedAt);
    
        // Trả về kết quả cho AJAX
        echo json_encode([
            'success' => true,
            'html' => $html,
            'hasMore' => checkMoreMainComment($conn,$postId,$lastCreatedAt)
        ]);
        exit;
    }

    // Kiểm tra xem còn comment mức CHA nữa ko ? Để hiển thị nút showMore
    function checkMoreMainComment($conn, $postId, $lastCreatedAt) {
        if($lastCreatedAt){
            $queryCheckMore = "
            SELECT 1
            FROM Comments 
            WHERE post_id = ? AND parent_id IS NULL AND created_at < ?
            LIMIT 1
            ";
        $stmt = $conn->prepare($queryCheckMore);
        $stmt->bind_param('is', $postId, $lastCreatedAt);
        }else{
            $queryCheckMore = "
            SELECT 1
            FROM Comments 
            WHERE post_id = ? AND parent_id IS NULL
            LIMIT 1
            ";
        $stmt = $conn->prepare($queryCheckMore);
        $stmt->bind_param('i', $postId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $hasMore = $result-> num_rows > 0;
        $stmt->close();
    
        return $hasMore; // Kiểm tra còn dữ liệu không
    }

    // Kiểm tra xem còn comment mức CON hay ko ? Để hiển thị nút showMore
    function checkMoreReplies($conn, $parentId, $lastCreatedAt) {
        if ($lastCreatedAt) {
            $query = "SELECT 1 FROM comments WHERE parent_id = ? AND created_at > ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('is', $parentId, $lastCreatedAt);
        } else {
            $query = "SELECT 1 FROM comments WHERE parent_id = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $parentId);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        $hasMore = $result->num_rows > 0;
        $stmt->close();
    
        return $hasMore;
    }

    // Chức năng hiển thị danh sách những người đã like comment
    if(isset($_POST['fetchUsersLikeComment'])){
        $commentId = intval($_POST['commentId']);
        $query = "SELECT u.user_id , u.full_name , u.avatar FROM `commentlikes` cl
                    JOIN users u on u.user_id = cl.user_id 
                    WHERE cl.comment_id = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $commentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $output = '';

        if($result -> num_rows > 0){
            while($row = $result->fetch_assoc()){
                $fullName = $row['full_name'];
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
                $output .= '<ul class="sidebar__wrapper-list">';
                $output .= "
                    <a href='$profileUrl'>
                                    <li class='sidebar__wrapper-item'>
                                        <img src='$avatar'
                                            alt='' />
                                        <span class='title'>$fullName</span>
                                    </li>
                    </a>
                ";
            }
            $output .= '</ul>';
        }

        echo $output;
    }
    