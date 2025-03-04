<?php
    require_once '../ajax_action.php';
    // Fetch Post
    if(isset($_POST["fetchPost"])){
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại
    
        // Truy vấn SQL để lấy thông tin bài viết cùng số lượt like, comment và trạng thái "đã like"
        $postQuery = "
        SELECT 
            p.*,
            u.full_name,
            u.avatar,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) AS total_likes,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) AS total_comments,
            (SELECT COUNT(*) FROM shares WHERE post_id = p.post_id) AS total_shares,
            CASE WHEN EXISTS (
            SELECT 1 FROM likes WHERE post_id = p.post_id AND user_id = ?
            ) THEN 1 ELSE 0 END AS user_liked
        FROM posts p
        INNER JOIN users u ON u.user_id = p.user_id
        ORDER BY p.created_at DESC
        ";
    
        $stmt = $conn->prepare($postQuery);
        $stmt->bind_param('i', $userId); // Gán user_id vào truy vấn
        $stmt->execute();
        $result = $stmt->get_result();
        $output = '';
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row['full_name']);
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
                $content = htmlspecialchars($row['content']);
                $maxLength = 300;
                $createdAt = formatTime($row['created_at']);
                $media = !empty($row['media_url']) ? $row['media_url'] : '';
    
                // Số lượng like và comment
                $totalLikes = intval($row['total_likes']);
                $totalComments = intval($row['total_comments']);
                $totalShares = intval($row['total_shares']);
    
                // Kiểm tra trạng thái đã like
                $userLiked = intval($row['user_liked']) === 1; // true nếu đã like
                $likeIcon = $userLiked ? 'fas fa-heart liked' : 'far fa-heart'; // Đổi icon nếu liked

                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
    
                // Hiển thị bài viết
                $output .= "
                <section class='post__box-post' data-post-id=".$row['post_id'].">
                    <div class='post__box-header'>
                        <div class='post__box-details'>
                            <a href='$profileUrl'>
                                <img src='$avatar' alt='$username'>
                            </a>
                            <div class='post__box-info'>
                            <a href='$profileUrl'>
                                <span>$username</span>
                            </a>
                                <div>$createdAt</div>
                            </div>
                        </div>
                        <div class='menu-btn'>
                            <span><i class='ri-more-line'></i></span>
                        </div>
                    </div>";
                // Hiển thị nút Xem thêm nếu Content quá dài
                if(mb_strlen($content) > $maxLength){
                    $contentSnippet = mb_substr($content, 0, $maxLength) . "...";
                    $output .= "
                    <div class='post__box-text' data-full-content='$content'>
                        $contentSnippet
                    </div>
                    <span class='read-more'>Xem thêm</span>";
                }else{
                    $output .= "<div class='post__box-text'>$content</div>"; // Hiển thị đầy đủ nếu ngắn
                }
    
                // Hiển thị media
                if (!empty($media)) {
                    $fileExtension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        $output .= "<div class='post__box-media_list'><img src='$media' alt='Post Media'></div>";
                    } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
                        // $videoHtml = renderCustomVideoPlayer($media);
                        $output .= "
                        <div class='post__box-media_list'>
                        <video src='$media' controls type='video/$fileExtension' muted />
                        </div>";
                    }
                }
    
                // Hiển thị số lượt like và comment
                $output .= "
                        <div class='post__box-emotion'>
                            <div class='post__box-like'>
                                <i class='fas fa-heart' style='color: red'></i>
                                <span>$totalLikes</span>
                            </div>
                            <div class='post__box-emotion_right'>
                                <div class='post__box-comment'>$totalComments comments</div>
                                <div class='post__box-share'>$totalShares shares</div>
                            </div>
                        </div>
                        <ul class='post__box-interaction_list'>
                            <li class='post__box-interaction_item like-btn'>
                                <span><i class='$likeIcon'></i> Like</span>
                            </li>
                            <li class='post__box-interaction_item comment-btn'>
                                <span><i class='far fa-comment'></i> Comment</span>
                            </li>
                            <li class='post__box-interaction_item share-btn'>
                                <span><i class='far fa-share-square'></i> Share</span>
                            </li>
                        </ul>
                    </div>
                    </section>";
            }
        }
        echo $output;
    }

    // Get Post By ID
    if(isset(($_POST['getPost']))){
        $userId = $_SESSION['user_id'];
        $postId = $_POST['postId'];

        $postQuery = "
            SELECT 
                p.*,
                u.full_name,
                u.avatar,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) AS total_likes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) AS total_comments,
                (SELECT COUNT(*) FROM shares WHERE post_id = p.post_id) AS total_shares,
                CASE WHEN EXISTS (
                    SELECT 1 FROM likes WHERE post_id = p.post_id AND user_id = ?
                ) THEN 1 ELSE 0 END AS user_liked
            FROM posts p
            INNER JOIN users u ON u.user_id = p.user_id
            WHERE p.post_id = ?  -- Lọc theo postId
            GROUP BY p.post_id, u.full_name, u.avatar
            ORDER BY p.created_at DESC;
        ";

        $stmt = $conn->prepare($postQuery);
        $stmt->bind_param('ii', $userId, $postId); // Gán user_id và post_id vào truy vấn
        $stmt->execute();
        $result = $stmt->get_result();
        $output = '';
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row['full_name']);
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
                $content = htmlspecialchars($row['content']);
                $maxLength = 300;
                $createdAt = formatTime($row['created_at']);
                $media = !empty($row['media_url']) ? $row['media_url'] : '';
    
                // Số lượng like và comment
                $totalLikes = intval($row['total_likes']);
                $totalComments = intval($row['total_comments']);
                $totalShares = intval($row['total_shares']);
    
                // Kiểm tra trạng thái đã like
                $userLiked = intval($row['user_liked']) === 1; // true nếu đã like
                $likeIcon = $userLiked ? 'fas fa-heart liked' : 'far fa-heart'; // Đổi icon nếu liked

                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
    
                // Hiển thị bài viết
                $output .= "
                <section class='post__box-post' style='width:60%;height: max-content;' data-post-id=".$row['post_id'].">
                    <div class='post__box-header'>
                        <div class='post__box-details'>
                            <a href='$profileUrl'>
                                <img src='$avatar' alt='$username'>
                            </a>
                            <div class='post__box-info'>
                            <a href='$profileUrl'>
                                <span>$username</span>
                            </a>
                                <div>$createdAt</div>
                            </div>
                        </div>
                        <div class='menu-btn'>
                            <span><i class='ri-more-line'></i></span>
                        </div>
                    </div>";
                // Hiển thị nút Xem thêm nếu Content quá dài
                if(mb_strlen($content) > $maxLength){
                    $contentSnippet = mb_substr($content, 0, $maxLength) . "...";
                    $output .= "
                    <div class='post__box-text' data-full-content='$content'>
                        $contentSnippet
                    </div>
                    <span class='read-more'>Xem thêm</span>";
                }else{
                    $output .= "<div class='post__box-text'>$content</div>"; // Hiển thị đầy đủ nếu ngắn
                }
    
                // Hiển thị media
                if (!empty($media)) {
                    $fileExtension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        $output .= "<div class='post__box-media_list'><img src='$media' alt='Post Media'></div>";
                    } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
                        $output .= "
                        <div class='post__box-media_list'>
                            <video controls muted>
                                <source src='$media' type='video/$fileExtension'>
                            </video>
                        </div>";
                    }
                }
    
                // Hiển thị số lượt like và comment
                $output .= "
                    <div class='post__box-emotion'>
                        <div class='post__box-like'>
                            <i class='fas fa-heart' style='color: red'></i>
                            <span>$totalLikes</span>
                        </div>
                        <div class='post__box-emotion_right'>
                            <div class='post__box-comment'>$totalComments comments</div>
                            <div class='post__box-share'>$totalShares shares</div>
                        </div>
                    </div>
                    <ul class='post__box-interaction_list'>
                        <li class='post__box-interaction_item like-btn'>
                            <span><i class='$likeIcon'></i> Like</span>
                        </li>
                        <li class='post__box-interaction_item'>
                            <span><i class='far fa-comment'></i> Comment</span>
                        </li>
                        <li class='post__box-interaction_item share-btn'>
                            <span><i class='far fa-share-square'></i> Share</span>
                        </li>
                    </ul>
                </div>
                <div class='comment__box' style='padding: 0 20px;'>
                    <div class='comment__box-wrapper'>
                        <textarea type='text' class='comment__input' rows='2' cols='50' style='resize: none;' placeholder='Write a reply...'></textarea>
                        <div class=\"emoji-icon\">
                            <img src=\"../icon/smile.png\" alt=\"\" style=\"max-width:18px;\" class=\"post__emoji\">
                            <div class=\"emoji-selector\">
                                <div class='input-container'>
                                    <input placeholder=\"Seach...\" />
                                </div>
                                <div class=\"emoji-loading\">🔄 Đang tải emoji...</div>
                                <ul class=\"emoji-list\" id='emojiList'></ul>
                            </div>
                        </div>
                        <div class='comment__box-action'>
                            <span class='comment__media'>
                                <label class='custom-file-label'>
                                  <i class='fas fa-camera-retro'></i>
                                  <input type='file' class='file-comment' accept='.jpg, .jpeg, .png' />
                                </label>
                            </span>
                            <span class='comment__submit'>
                                <i class='fas fa-paper-plane'></i>
                            </span>
                        </div>
                    </div>
                </div>";
                
                // Danh sách các bình luận
                $output .= '<ul class="post__box-comment_list">';
                // Hàm trả về các bình luận
                $commentsHtml = fetchAndRenderComments($conn, $postId); // Lấy danh sách bình luận
                $output .= $commentsHtml;
                $output .= '</ul>';

                // Nút show More Comment
                if(!empty($commentsHtml))
                $output .=
                '<div id="load-more-comments">
                    <button id="load-more-btn">Tải thêm bình luận</button>
                </div>';    

                
                $output .= '</section>';
            }
        }
        echo $output;
    }

    // Get Post And Share by ID
    if(isset($_POST['fetchPostsAndShares'])){
        $userId = $_POST['userId'];

        $query = "
        -- Bài viết gốc
        SELECT 
            p.post_id,
            p.user_id,
            p.content,
            p.media_url,
            p.created_at AS time,
            '' AS share_content,
            'original' AS post_type,
            u.full_name,
            u.avatar,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) AS total_likes,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) AS total_comments,
            (SELECT COUNT(*) FROM shares WHERE post_id = p.post_id) AS total_shares,
            CASE WHEN EXISTS (
                SELECT 1 FROM likes WHERE post_id = p.post_id AND user_id = ?
            ) THEN 1 ELSE 0 END AS user_liked
        FROM Posts p
        INNER JOIN Users u ON u.user_id = p.user_id
        WHERE p.user_id = ?
        
        UNION
        
        -- Bài viết chia sẻ
        SELECT 
            p.post_id,
            p.user_id,
            p.content,
            p.media_url,
            s.shared_at AS time,
            s.content AS share_content,
            'shared' AS post_type,
            u.full_name,
            u.avatar,
            0 AS total_likes, -- Không hiển thị cho bài chia sẻ
            0 AS total_comments, -- Không hiển thị cho bài chia sẻ
            0 AS total_shares, -- Không hiển thị cho bài chia sẻ
            0 AS user_liked -- Không hiển thị cho bài chia sẻ
        FROM Shares s
        INNER JOIN Posts p ON p.post_id = s.post_id
        INNER JOIN Users u ON u.user_id = p.user_id
        WHERE s.user_id = ?
        
        ORDER BY time DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('iii', $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $output = '';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row['full_name']);
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
                $content = htmlspecialchars($row['content']);
                $maxLength = 300;
                $time = formatTime($row['time']);
                $media = !empty($row['media_url']) ? $row['media_url'] : '';
                $profileUrl = "profile.php?user_id=" . $row['user_id'];
                $postType = $row['post_type'];

                if ($postType === 'original') {
                    // Bài viết gốc
                    $totalLikes = intval($row['total_likes']);
                    $totalComments = intval($row['total_comments']);
                    $totalShares = intval($row['total_shares']);
                    $userLiked = intval($row['user_liked']) === 1;
                    $likeIcon = $userLiked ? 'fas fa-heart liked' : 'far fa-heart';

                    $output .= "
                    <section class='post__box-post' data-post-id='{$row['post_id']}'>
                        <div class='post__box-header'>
                            <div class='post__box-details'>
                                <a href='$profileUrl'><img src='$avatar' alt='$username'></a>
                                <div class='post__box-info'>
                                    <a href='$profileUrl'><span>$username</span></a>
                                    <div>$time</div>
                                </div>
                            </div>
                            <div class='menu-btn'><span><i class='ri-more-line'></i></span></div>
                        </div>";
                    if (mb_strlen($content) > $maxLength) {
                        $contentSnippet = mb_substr($content, 0, $maxLength) . "...";
                        $output .= "<div class='post__box-text' data-full-content='$content'>$contentSnippet</div><span class='read-more'>Xem thêm</span>";
                    } else {
                        $output .= "<div class='post__box-text'>$content</div>";
                    }
                    if (!empty($media)) {
                        $fileExtension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                            $output .= "<div class='post__box-media_list'><img src='$media' alt='Post Media'></div>";
                        } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
                            $output .= "<div class='post__box-media_list'><video src='$media' controls type='video/$fileExtension' muted /></div>";
                        }
                    }
                    $output .= "
                        <div class='post__box-emotion'>
                            <div class='post__box-like'><i class='fas fa-heart' style='color: red'></i><span>$totalLikes</span></div>
                            <div class='post__box-emotion_right'>
                                <div class='post__box-comment'>$totalComments comments</div>
                                <div class='post__box-share'>$totalShares shares</div>
                            </div>
                        </div>
                        <ul class='post__box-interaction_list'>
                            <li class='post__box-interaction_item like-btn'><span><i class='$likeIcon'></i> Like</span></li>
                            <li class='post__box-interaction_item comment-btn'><span><i class='far fa-comment'></i> Comment</span></li>
                            <li class='post__box-interaction_item share-btn'><span><i class='far fa-share-square'></i> Share</span></li>
                        </ul>
                    </div>
                    </section>";
                } else {

                    $shareContent = $row['share_content'];

                    // Bài viết chia sẻ
                    $output .= "
                    <section class='post__box-post'>
                        <div class='post__box-header'>
                            <div class='post__box-details'>
                                <a href='$profileUrl'>
                                    <img src='$avatar' alt='$username' />
                                </a>
                                <div class='post__box-info'>
                                    <a href='$profileUrl'>
                                        <span>$username</span>
                                    </a>
                                    <div>$time (Đã chia sẻ)</div>
                                </div>
                            </div>
                            <div class='menu-btn'>
                                <span><i class='ri-more-line'></i></span>
                            </div>
                        </div>
                        <div class='post__box-content'>";
                        if(mb_strlen($shareContent) > $maxLength){
                            $contentSnippet = mb_substr($shareContent,0,$maxLength) . "...";
                            $output .= "<p class='post__box-text' data-full-content='$shareContent'>$contentSnippet</p>
                                <span class='read-more'>Xem thêm</span>";
                        }else{
                            $output .= "<p class='post__box-text'>$shareContent</p>";
                        }
                        $output .= "<div class='post__box-media_list' style='margin: 0'>";
                    if (!empty($media)) {
                        $fileExtension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                            $output .= "<img src='$media' alt='Post Media' />";
                        } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
                            $output .= "<video src='$media' controls type='video/$fileExtension' muted />";
                        }
                    }
                    $output .= "
                            </div>
                            <div class='post__box-details' style='margin: 0 10px'>
                                <a href='$profileUrl'>
                                    <img src='$avatar' alt='$username' />
                                </a>
                                <div class='post__box-info'>
                                    <a href='$profileUrl'>
                                        <span>$username</span>
                                    </a>
                                    <div>$time</div>
                                </div>
                            </div>";
                    // Thêm nút "Xem thêm" cho nội dung chia sẻ
                    if (mb_strlen($content) > $maxLength) {
                        $contentSnippet = mb_substr($content, 0, $maxLength) . "...";
                        $output .= "
                        <p class='post__box-text' data-full-content='$content'>$contentSnippet</p>
                        <span class='read-more'>Xem thêm</span>";
                    } else {
                        $output .= "<p class='post__box-text'>$content</p>";
                    }
                    $output .= "
                        </div>
                    </section>";
                }
            }
        } else {
            $output .= "<p>Không có bài viết nào.</p>";
        }
        echo $output;
    }

    // Create Post
    if(isset($_POST['createPost'])){
        $content = $_POST['content'];
        $media_url = $_POST['media_url'];
        $user_id = $_SESSION['user_id'];
    
        $query = "INSERT INTO `posts`(`user_id`, `content`, `media_url`)
        VALUES ('$user_id','$content','$media_url')";
    
        $result = $conn->query($query);
    
        if($result){
            echo 'true';
        }else{
                echo 'false';
        }
    }

    // Show Menu Post
    if(isset($_POST['showMenuPost'])){
        $userId = $_SESSION['user_id'];
        $postId = $_POST['postId'];

        $checkQuery =" SELECT 
            (SELECT 1 FROM posts WHERE post_id = ? AND user_id = ? LIMIT 1) AS is_owner,
            (SELECT 1 FROM savedposts WHERE post_id = ? AND user_id = ? LIMIT 1) AS is_saved
        ";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param('iiii', $postId, $userId, $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $isOwner = $row['is_owner'];
        $isSaved = $row['is_saved'];

        $output = '';
        // Nếu là bài viết của người dùng hiện tại
        if($isOwner){
            $element = $isSaved ? '<i class=\'ri-bookmark-2-fill\'></i> <span>Hủy lưu bài viết</span>' : '<i class=\'ri-bookmark-fill\'></i> <span>Lưu bài viết</span>'; 

            $output .= "
                <div class='menu__post'>
                    <ul class='menu__post-list'>
                        <li class='menu__post-item save-post'> $element</li>
                        <li class='menu__post-item edit-post'><i class='ri-pencil-fill'></i> <span>Chỉnh sửa bài viết</span></li>
                        <li class='menu__post-item delete-post'><i class='ri-delete-bin-fill'></i> <span>Xóa bài viết</span></li>
                    </ul>
                </div>
            ";
        }else{
            $element = $isSaved ? '<i class=\'ri-bookmark-2-fill\'></i><span>Hủy lưu bài viết</span>' : '<i class=\'ri-bookmark-fill\'></i><span>Lưu bài viết</span>'; 
            $output .= "
                <div class='menu__post'>
                    <ul class='menu__post-list'>
                        <li class='menu__post-item save-post'> $element</li>
                    </ul>
                </div>
            ";
        }

        echo $output;
    }

    // Save Post
    if (isset($_POST["savePost"])) {
        $userId = $_SESSION["user_id"]; // ID người dùng hiện tại
        $postId = $_POST["postId"]; // ID bài viết
    
        // Kiểm tra xem bài viết đã được lưu chưa
        $checkQuery = "SELECT 1 FROM savedposts WHERE user_id = ? AND post_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Nếu đã lưu, thực hiện hủy lưu
            $deleteQuery = "DELETE FROM savedposts WHERE user_id = ? AND post_id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("ii", $userId, $postId);
            $stmt->execute();
    
            // Trả về phản hồi JSON
            echo json_encode([
                "success" => true,
                "is_saved" => false, // Không còn lưu bài viết
            ]);
        } else {
            // Nếu chưa lưu, thực hiện lưu
            $insertQuery = "INSERT INTO savedposts (user_id, post_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ii", $userId, $postId);
            $stmt->execute();
    
            // Trả về phản hồi JSON
            echo json_encode([
                "success" => true,
                "is_saved" => true, // Đã lưu bài viết
            ]);
        }
        exit;
    }

    // Show Edit Post
    if (isset($_POST["showEditPost"])) {
        $userId = $_SESSION["user_id"];
        $postId = $_POST["postId"];
    
        $query = "SELECT p.content, p.media_url, u.user_id, u.full_name, u.avatar 
                  FROM posts p
                  JOIN users u ON u.user_id = p.user_id
                  WHERE p.post_id = ?";
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $postId); // Truyền biến $postId vào truy vấn
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc(); // Lấy dữ liệu hàng đầu tiên
            echo json_encode([
                "success" => true,
                "response" => [$row]
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Post not found."
            ]);
        }
    }

    // Edit Post
    if (isset($_POST["editPost"])) {
        
        $userId = $_SESSION["user_id"];
        $postId = $_POST["postId"];
        $content = $_POST["content"];
        $mediaUrl = isset($_POST["mediaUrl"]) ? $_POST["mediaUrl"] : null;
        $isMediaDeleted = isset($_POST["deleteMedia"]) ? true : false;
    
        // Lấy media cũ để kiểm tra có thay đổi không
        $query = "SELECT media_url FROM posts WHERE post_id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        
        $oldMediaUrl = $post["media_url"];
    
        // Xác định media mới cần cập nhật
        $newMediaUrl = $oldMediaUrl; // Mặc định giữ nguyên
        if ($isMediaDeleted) {
            $newMediaUrl = ''; // Nếu xóa media
        } elseif ($mediaUrl) {
            $newMediaUrl = $mediaUrl; // Nếu có media mới
        }
    
        // Cập nhật nội dung và media nếu có thay đổi
        $updateQuery = "UPDATE posts SET content = ?, media_url = ?, updated_at = NOW() WHERE post_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssi", $content, $newMediaUrl, $postId);
        $result = $stmt->execute();
    
        if ($result) {
            echo json_encode([
                "success" => true,
                "postId" => $postId,
                "content" => $content,
                "mediaUrl" => $newMediaUrl,
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Failed to update the post.",
            ]);
        }
        exit;
    }
    
    // Delete Post
    if(isset($_POST["deletePost"])) {
        $postId = $_POST["postId"];

        $deleteQuery = "DELETE FROM posts WHERE post_id = '$postId' LIMIT 1";
        $result = mysqli_query($conn, $deleteQuery);

        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    // Like Post
    if (isset($_POST['likePost'])) {
        $postId = $_POST['post_id'];
        $userId = $_SESSION['user_id']; // ID người dùng

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
    
        // Kiểm tra xem người dùng đã like bài viết hay chưa
        $checkLikeQuery = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
        $stmt = $conn->prepare($checkLikeQuery);
        $stmt->bind_param("ii", $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Nếu đã like, xóa like
            $deleteLikeQuery = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
            $stmt = $conn->prepare($deleteLikeQuery);
            $stmt->bind_param("ii", $postId, $userId);
            $stmt->execute();

            // Xóa thông báo like tương ứng
            $deleteNotifQuery = "DELETE FROM Notifications WHERE user_id = ? AND sender_id = ? AND notification_type = 'like' AND reference_id = ?";
            $stmt = $conn->prepare($deleteNotifQuery);
            $stmt->bind_param("iii", $postOwner, $userId, $postId);
            $stmt->execute();
    
            // Trả về trạng thái "unliked"
            $response = [
                'status' => 'unliked',
                'likeCount' => getLikeCount($conn, $postId) // Hàm lấy tổng số lượt like
            ];
        } else {
            // Nếu chưa like, thêm like
            $insertLikeQuery = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insertLikeQuery);
            $stmt->bind_param("ii", $postId, $userId);
            $stmt->execute();

            // Nếu là người dùng KHÁC -> LIKE thì ko thông báo
            if($userId != $postOwner) {
                $notifQuery = "INSERT INTO Notifications (user_id, sender_id, notification_type, content, reference_id) 
                VALUES (?, ?, 'like', ?, ?)";
                $notifStmt = $conn->prepare($notifQuery);
                $notifContent = "đã thích bài viết của bạn: \"$postContentSnippet\"";
                $notifStmt->bind_param("iisi", $postOwner, $userId, $notifContent, $postId);
                $notifStmt->execute();
            }

            // Trả về trạng thái "liked"
            $response = [
                'status' => 'liked',
                'likeCount' => getLikeCount($conn, $postId) // Hàm lấy tổng số lượt like
            ];
        }
    
        // Trả về JSON phản hồi
        echo json_encode($response);
        exit();
    }

    // Share Post
    if(isset($_POST['sharePost'])){
        $userId = $_SESSION['user_id'];
        $postId = $_POST['postId'];
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        // Kiem tra bai viet ton tai ko
        $checkPostQuery = "SELECT * FROM posts WHERE post_id = ?";
        $stmt = $conn->prepare($checkPostQuery);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo json_encode(["success" => false, "message" => "Bài viết không tồn tại!"]);
            exit();
        }

        $postData = $result->fetch_assoc();
        $postOwner = $postData['user_id']; // ID chủ bài viết
        $postContent = isset($postData['content']) ? htmlspecialchars($postData['content']) : "Bài viết này không tồn tại.";
        // Giới hạn nội dung bài viết hiển thị trong thông báo (50 ký tự)
        $postContentSnippet = mb_strimwidth($postContent, 0, 50, "...");

        // Chia se bai viet
        $insertShareQuery = "INSERT INTO shares (user_id, post_id, content) VALUES (?, ?, ?);";
        $stmt = $conn->prepare($insertShareQuery);
        $stmt->bind_param("iis", $userId, $postId, $content);
        $stmt->execute();

        // Gửi thông báo cho chủ bài viết này
        if($userId !== $ownerPostId){
            $insertNotification = "INSERT INTO notifications (user_id, sender_id, notification_type, content)
            VALUES (?, ?, 'share', ?)";
            $stmt = $conn->prepare($insertNotification);
            $notifContent = "đã chia sẻ bài viết của bạn: \"$postContentSnippet\"";
            $stmt->bind_param("iis", $ownerPostId, $userId, $notifContent);
            $stmt->execute();
        }
        
        echo json_encode(["success" => true, "message" => "Chia sẻ bài viết thành công!"]);
        exit();
    }

    // Hàm lấy tổng số lượt like
    function getLikeCount($conn, $postId) {
        $likeCountQuery = "SELECT COUNT(*) AS total_likes FROM likes WHERE post_id = ?";
        $stmt = $conn->prepare($likeCountQuery);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return intval($row['total_likes']);
    }

    // Show Post
    if (isset($_POST['showPost'])) {
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại
        $postId = $_POST['post_id'];
        $limit = 2;
    
        // Truy vấn SQL để lấy thông tin bài viết cùng số lượt like, comment và trạng thái "đã like"
        $postQuery = "
            SELECT 
                p.*,
                u.full_name,
                u.avatar,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) AS total_likes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) AS total_comments,
                (SELECT COUNT(*) FROM shares WHERE post_id = p.post_id) AS total_shares,
                CASE WHEN EXISTS (
                    SELECT 1 FROM likes WHERE post_id = p.post_id AND user_id = ?
                ) THEN 1 ELSE 0 END AS user_liked
            FROM posts p
            INNER JOIN users u ON u.user_id = p.user_id
            WHERE p.post_id = ?  -- Lọc theo postId
            GROUP BY p.post_id, u.full_name, u.avatar
            ORDER BY p.created_at DESC;
        ";
    
        $stmt = $conn->prepare($postQuery);
        $stmt->bind_param('ii', $userId, $postId); // Gán user_id và post_id vào truy vấn
        $stmt->execute();
        $result = $stmt->get_result();
        $output = '';
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row['full_name']);
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
                $content = htmlspecialchars($row['content']);
                $maxLength = 300;
                $createdAt = formatTime($row['created_at']);
                $media = !empty($row['media_url']) ? $row['media_url'] : '';
    
                // Số lượng like và comment
                $totalLikes = intval($row['total_likes']);
                $totalComments = intval($row['total_comments']);
                $totalShares = intval($row['total_shares']);
    
                // Kiểm tra trạng thái đã like
                $userLiked = intval($row['user_liked']) === 1; // true nếu đã like
                $likeIcon = $userLiked ? 'fas fa-heart liked' : 'far fa-heart'; // Đổi icon nếu liked

                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
    
                // Hiển thị bài viết
                $output .= "
                <section class='post__box-post' style='max-height: 90vh;' data-post-id='$postId'>
                    <header class='post__box-title'>$username's post</header>
                    <div class='post__box-header'>
                        <div class='post__box-details'>
                            <a href='$profileUrl'>
                                <img src='$avatar' alt='$username'>
                            </a>
                            <div class='post__box-info'>
                            <a href='$profileUrl'>
                                <span>$username</span>
                            </a>
                                <div>$createdAt</div>
                            </div>
                        </div>
                        <div class='menu-btn'>
                            <span><i class='ri-more-line'></i></span>
                        </div>
                    </div>";
                // Hiển thị nút Xem thêm nếu Content quá dài
                if(mb_strlen($content) > $maxLength){
                    $contentSnippet = mb_substr($content, 0, $maxLength) . "...";
                    $output .= "
                    <div class='post__box-text' data-full-content='$content'>
                        $contentSnippet
                    </div>
                    <span class='read-more'>Xem thêm</span>";
                }else{
                    $output .= "<div class='post__box-text'>$content</div>"; // Hiển thị đầy đủ nếu ngắn
                }
    
                // Hiển thị media
                if (!empty($media)) {
                    $fileExtension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        $output .= "<div class='post__box-media_list'><img src='$media' alt='Post Media'></div>";
                    } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
                        $output .= "
                        <div class='post__box-media_list'>
                            <video controls muted>
                                <source src='$media' type='video/$fileExtension'>
                            </video>
                        </div>";
                    }
                }
    
                // Hiển thị số lượt like và comment
                $output .= "
                    <div class='post__box-emotion'>
                        <div class='post__box-like'>
                            <i class='fas fa-heart' style='color: red'></i>
                            <span>$totalLikes</span>
                        </div>
                        <div class='post__box-emotion_right'>
                            <div class='post__box-comment'>$totalComments comments</div>
                            <div class='post__box-share'>$totalShares shares</div>
                        </div>
                    </div>
                    <ul class='post__box-interaction_list'>
                        <li class='post__box-interaction_item like-btn'>
                            <span><i class='$likeIcon'></i> Like</span>
                        </li>
                        <li class='post__box-interaction_item'>
                            <span><i class='far fa-comment'></i> Comment</span>
                        </li>
                        <li class='post__box-interaction_item share-btn'>
                            <span><i class='far fa-share-square'></i> Share</span>
                        </li>
                    </ul>
                </div>
                <div class='comment__box' style='padding: 0 20px;'>
                    <div class='comment__box-wrapper'>
                        <textarea type='text' class='comment__input' rows='2' cols='50' style='resize: none;' placeholder='Write a comment...'></textarea>
                        <div class=\"emoji-icon\">
                            <img src=\"../icon/smile.png\" alt=\"\" style=\"max-width:18px;\" class=\"post__emoji\">
                            <div class=\"emoji-selector\">
                                <div class='input-container'>
                                    <input placeholder=\"Seach...\" />
                                </div>
                                <div class=\"emoji-loading\">🔄 Đang tải emoji...</div>
                                <ul class=\"emoji-list\" id='emojiList'></ul>
                            </div>
                        </div>
                        <div class='comment__box-action'>
                            <span class='comment__media'>
                                <label class='custom-file-label'>
                                  <i class='fas fa-camera-retro'></i>
                                  <input type='file' class='file-comment' accept='.jpg, .jpeg, .png' />
                                </label>
                            </span>
                            <span class='comment__submit'>
                                <i class='fas fa-paper-plane'></i>
                            </span>
                        </div>
                    </div>
                </div>";
                
                // Danh sách các bình luận
                $output .= '<ul class="post__box-comment_list">';
                // Hàm trả về các bình luận
                $commentsHtml = fetchAndRenderComments($conn, $postId); // Lấy danh sách bình luận
                $output .= $commentsHtml;
                $output .= '</ul>';

                // Nút show More Comment
                if(!empty($commentsHtml))
                $output .=
                '<div id="load-more-comments">
                    <button id="load-more-btn">Tải thêm bình luận</button>
                </div>';    

                
                $output .= '</section>';
            }
        }
        echo $output;
    }

    // Refresh Post sau khi đóng modal show Post
    if (isset($_POST['refreshPost'])) {
        $postId = intval($_POST['post_id']);
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại
    
        // Truy vấn SQL để lấy thông tin bài viết cùng số lượt like, comment và trạng thái "đã like"
        $postQuery = "
            SELECT 
                p.*,
                u.full_name,
                u.avatar,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) AS total_likes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) AS total_comments,
                (SELECT COUNT(*) FROM shares WHERE post_id = p.post_id) AS total_shares,
                EXISTS (
                    SELECT 1 
                    FROM likes l 
                    WHERE l.post_id = p.post_id AND l.user_id = ?
                ) AS user_liked
            FROM posts p
            INNER JOIN users u ON u.user_id = p.user_id
            WHERE p.post_id = ?";
    
        $stmt = $conn->prepare($postQuery);
        $stmt->bind_param('ii', $userId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '';
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
    
            // Dữ liệu của bài viết
            $username = htmlspecialchars($row['full_name']);
            $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png';
            $content = htmlspecialchars($row['content']);
            $maxLength = 300;
            $createdAt = formatTime($row['created_at']);
            $media = !empty($row['media_url']) ? $row['media_url'] : '';

            // Số lượng like và comment
            $totalLikes = intval($row['total_likes']);
            $totalComments = intval($row['total_comments']);
            $totalShares = intval($row['total_shares']);

            // Kiểm tra trạng thái đã like
            $userLiked = intval($row['user_liked']) === 1; // true nếu đã like
            $likeIcon = $userLiked ? 'fas fa-heart liked' : 'far fa-heart'; // Đổi icon nếu liked

            $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
    
            // Hiển thị bài viết
            $output .= "
            <section class='post__box-post' data-post-id='$postId'>
                <div class='post__box-header'>
                    <div class='post__box-details'>
                        <a href='$profileUrl'>
                            <img src='$avatar' alt='$username'>
                        </a>
                        <div class='post__box-info'>
                        <a href='$profileUrl'>
                            <span>$username</span>
                        </a>
                            <div>$createdAt</div>
                        </div>
                    </div>
                    <div class='menu-btn'>
                        <span><i class='ri-more-line'></i></span>
                    </div>
                </div>";
                
            // Hiển thị nút Xem thêm nếu Content quá dài
            if(mb_strlen($content) > $maxLength){
                $contentSnippet = mb_substr($content, 0, $maxLength) . "...";
                $output .= "
                <div class='post__box-text' data-full-content='$content'>
                    $contentSnippet
                </div>
                <span class='read-more'>Xem thêm</span>";
            }else{
                $output .= "<div class='post__box-text'>$content</div>"; // Hiển thị đầy đủ nếu ngắn
            }
    
            // Hiển thị media
            if (!empty($media)) {
                $fileExtension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                    $output .= "<div class='post__box-media_list'><img src='$media' alt='Post Media'></div>";
                } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
                    $output .= "
                    <div class='post__box-media_list'>
                        <video controls muted>
                            <source src='$media' type='video/$fileExtension'>
                        </video>
                    </div>";
                }
            }
    
            // Hiển thị số lượt like và comment
            $output .= "
            <div class='post__box-emotion'>
                <div class='post__box-like'>
                    <i class='fas fa-heart' style='color: red'></i>
                    <span>$totalLikes</span>
                </div>
                <div class='post__box-emotion_right'>
                    <div class='post__box-comment'>$totalComments comments</div>
                    <div class='post__box-share'>$totalShares shares</div>
                </div>
            </div>
            <ul class='post__box-interaction_list'>
                <li class='post__box-interaction_item like-btn'>
                    <span><i class='$likeIcon'></i> Like</span>
                </li>
                <li class='post__box-interaction_item comment-btn'>
                    <span><i class='far fa-comment'></i> Comment</span>
                </li>
                <li class='post__box-interaction_item share-btn'>
                    <span><i class='far fa-share-square'></i> Share</span>
                </li>
            </ul>
            </div>";
            $output .= '</section>';
        }
        echo $output;
    }

    //  Lấy danh sách User đã like Post
    if(isset($_POST['fetchUsersLike'])){
        $postId = $_POST['postId'];

        $query = 'SELECT u.user_id, u.full_name ,u.avatar FROM users u
            join likes l on l.user_id = u.user_id
            where l.post_id = ?;';
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $postId);
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


    