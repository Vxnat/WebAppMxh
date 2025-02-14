<?php
    require_once 'db_connection.php';
    session_start();
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    
    // Xử lý thanh html để hiển thị các bình luận
    function renderComments($comments) {
        $html = '';
        foreach ($comments as $comment) {
            $username = htmlspecialchars($comment['username']);
            $avatar = !empty($comment['user_avatar']) ? $comment['user_avatar'] : '../img/default-avatar.png';
            $text = htmlspecialchars($comment['comment_text']);
            $totalLike = intval($comment['total_like']);
            $createdAt = formatTime($comment['created_at']);
            $img = $comment['comment_img'];
            $imgElement = $img != null ? "<img src=$img alt=\"\" class=\"comment-img\">" : null;
            $isLiked = $comment['user_has_liked'] ? 'comment-liked' : '' ;
    
            $html .= "
                <li class='post__box-comment_item' data-comment-id='{$comment['comment_id']}'
                data-user-id='{$comment['user_id']}' data-created-at='{$comment['created_at']}'>
                    <div class='post__box-comment_content'>
                        <img src='$avatar' alt='$username' class='comment-avatar'>
                        <div class='post__box-comment_details'>
                            <div class='post__box-comment_text'>
                                <span>$username</span>
                                <div>$text</div>
                            </div>
                            $imgElement
                            <div class='post__box-comment_interaction'>
                                <div class='post__box-action'>
                                    <span>$createdAt</span>
                                    <span class='like-comment-btn $isLiked' >Like</span>
                                    <span class='reply-btn'>Reply</span>
                                    <span class='total-like'>$totalLike <i class=\"fas fa-heart\" style=\"color:red\"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>";
            
                
    
            // Nếu có bình luận con, thêm nút "Xem các bình luận" nhưng không render bình luận con ngay
            if ($comment['has_replies']) {
                $html .= "
                    <div class='post__box-comment_view-replies'>
                        <button type=\"button\" class='view-replies-btn' data-parent-id='{$comment['comment_id']}'>Xem các bình luận</button>
                    </div>";
            }

            $html .= "</li>";
        }
        return $html;
    }

    // Lấy dữ liệu từ SQL cho commet cua cac POST
    function fetchAndRenderComments($conn, $postId, $limit = 2, $lastCreatedAt = null) {
        $user_id = $_SESSION['user_id'];
    
        $query = "
            SELECT 
                c.comment_id,
                c.post_id,
                c.user_id,
                c.comment_text,
                c.comment_img,
                c.parent_id,
                COUNT(cl.like_id) AS total_like,
                c.created_at,
                u.full_name AS username,
                u.avatar AS user_avatar,
                EXISTS (
                    SELECT 1 FROM Comments c2 WHERE c2.parent_id = c.comment_id
                ) AS has_replies,
                EXISTS (
                    SELECT 1 FROM CommentLikes cl2 WHERE cl2.comment_id = c.comment_id AND cl2.user_id = ?
                ) AS user_has_liked
            FROM Comments c
            JOIN Users u ON c.user_id = u.user_id
            LEFT JOIN CommentLikes cl ON c.comment_id = cl.comment_id
            WHERE c.post_id = ? AND c.parent_id IS NULL
        ";
        
        // Nếu có `lastCreatedAt`, chỉ lấy bình luận cũ hơn
        if ($lastCreatedAt) {
            $query .= " AND c.created_at < ? ";
        }
    
        $query .= " GROUP BY c.comment_id ORDER BY c.created_at DESC LIMIT ?";
    
        $stmt = $conn->prepare($query);
    
        if ($lastCreatedAt) {
            $stmt->bind_param('issi', $user_id, $postId, $lastCreatedAt, $limit);
        } else {
            $stmt->bind_param('iii', $user_id, $postId, $limit);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
    
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    
        $stmt->close();
    
        return renderComments($comments);
    }

    // Fetch Info User
    if(isset($_POST["fetchInfoUser"])){
        $user_id = $_SESSION['user_id']; // ID người dùng hiện tại
        $query = "SELECT full_name,avatar,bg_image,
        (SELECT COUNT(*) FROM posts WHERE user_id = $user_id) AS total_posts,
        (SELECT COUNT(*) FROM Friendships 
        WHERE (user_id = $user_id OR friend_id = $user_id) 
        AND status = 'accepted') AS total_friends,
        DATEDIFF(NOW(), (SELECT created_at FROM users WHERE user_id = $user_id)) AS total_days
        FROM users WHERE user_id = '$user_id' LIMIT 1";
        $result = mysqli_query($conn, $query);

        $output = '';
        if($result -> num_rows > 0){
            $row = $result -> fetch_assoc();
            $fullName = $row['full_name'];
            $background = !empty($row['bg_image']) ? $row['bg_image'] :'../img/default_bg.jpg';
            $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
            $total_posts = $row['total_posts'];
            $total_friends = $row['total_friends'];
            $total_days = $row['total_days'];
            
            $output .= '
                        <div class="sidebar__info-image">
                            <div class="sidebar__info-bg">
                                <img src="'.$background.'"
                                    alt="" />
                            </div>
                            <div class="sidebar__info-avatar">
                                <img src="'.$avatar.'"
                                    alt="" />
                            </div>
                        </div>
                        <div class="sidebar__info-content">
                            <h3 class="sidebar__infor-name">'.$fullName.'</h3>
                            <ul class="sidebar__infor-interation_list">
                                <li class="sidebar__infor-interation_item">
                                    <span class="quantity">'.$total_posts.'</span>
                                    <span class="name">Post</span>
                                </li>
                                <li class="sidebar__infor-interation_item">
                                    <span class="quantity">'.$total_friends.'</span>
                                    <span class="name">Friends</span>
                                </li>
                                <li class="sidebar__infor-interation_item">
                                    <span class="quantity">'.$total_days.'</span>
                                    <span class="name">Days</span>
                                </li>
                            </ul>
                            <button type="button" class="sidebar__infor-btn">My Profile</button>
                        </div>
            ';
        }

        echo $output;
    }

    // FormatTime từ timestamp
    function formatTime($timestamp) {
        // Chuyển đổi chuỗi datetime thành timestamp nếu cần
        $time = strtotime($timestamp);

        // Kiểm tra nếu chuỗi không thể chuyển thành timestamp hợp lệ
        if ($time === false) {
            return "Thời gian không hợp lệ";
        }
    
        $currentTime = time();
        $timeDifference = $currentTime - $time;

        // Các giá trị thời gian cơ bản
        $seconds = $timeDifference;
        $minutes = floor($seconds / 60);
        $hours = floor($seconds / 3600);
        $days = floor($seconds / 86400);
        $weeks = floor($seconds / 604800);
        $months = floor($seconds / 2592000);
    
        // Kiểm tra các khoảng thời gian để trả về kết quả phù hợp
        if ($seconds < 60) {
            return "Vài giây trước";
        } elseif ($minutes < 60) {
            return "$minutes phút trước";
        } elseif ($hours < 24) {
            return "$hours giờ trước";
        } elseif ($days == 1) {
            return "Hôm qua";
        } elseif ($days < 7) {
            return "$days ngày trước";
        } elseif ($weeks < 4) {
            return "$weeks tuần trước";
        } elseif ($months < 12) {
            return "$months tháng trước";
        } else {
            return date("d/m/Y", $time); // Hiển thị ngày/tháng/năm nếu thời gian lâu hơn 1 năm
        }
    }
    


    