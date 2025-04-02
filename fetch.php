<?php
ob_start();
include 'connect.php';

$query = "SELECT sp.saved_id, sp.post_id, sp.saved_at, 
                 u.full_name, p.content , p.media_url
          FROM savedposts sp
          JOIN posts p ON sp.post_id = p.post_id 
          JOIN users u ON p.user_id = u.user_id 
          ORDER BY sp.saved_at DESC";

$result = mysqli_query($conn, $query);
$output = '';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $content = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
        $short_content = (mb_strlen($content) > 200) 
            ? mb_substr($content, 0, 200) . '...' 
            : $content;
        $media_item = '';
        if (!empty($row['media_url'])) {
            $type = strtolower(pathinfo($row['media_url'], PATHINFO_EXTENSION));
            in_array($type, ['jpg', 'jpeg', 'png'])
            ? $media_item = '<div class="post-image"><img src='.$row['media_url'].' alt="null" loading="lazy"></div>'
            : $media_item = '<div class="post-image"><video src='.$row['media_url'].' /></div>';
        }

        $output .= '<li class="saved-post" data-id="' . $row['saved_id'] . '">';
        $output .= $media_item;
        $output .= '<div class="post-content">';
        $output .= '<h3>' . $short_content . '</h3>';
        $output .= '<p class="post-author"><span>Người đăng:</span> ' . htmlspecialchars($row['full_name']) . '</p>';
        $output .= '<p class="post-date"><span>Ngày lưu:</span> ' . date('d/m/Y H:i', strtotime($row['saved_at'])) . '</p>';
        $output .= '<button class="delete-btn" data-id="' . $row['saved_id'] . '">Xóa</button>';
        $output .= '</div>';
        $output .= '</li>';
    }
} else {
    $output .= '<div class="no-posts">Chưa có bài viết nào được lưu.</div>';
}

echo $output;
ob_end_flush();
?>