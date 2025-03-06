<?php
ob_start();
include 'connect.php';

$query = "SELECT savedposts.saved_id, savedposts.post_id, savedposts.saved_at, users.full_name, posts.content 
          FROM savedposts 
          JOIN posts ON savedposts.post_id = posts.post_id 
          JOIN users ON posts.user_id = users.user_id 
          ORDER BY savedposts.saved_at DESC";

$result = mysqli_query($conn, $query);

if ($result->num_rows > 0) {
    echo '<div class="saved-posts-container">';
    while ($row = $result->fetch_assoc()) {

        echo '<div class="saved-post">';
        echo '<h3>Nội dung bài viết: ' . htmlspecialchars($row["content"]) . '</h3>';
        echo '<p><strong>Người đăng:</strong> ' . htmlspecialchars($row["full_name"]) . '</p>';
        echo '<p><strong>Ngày lưu:</strong> ' . $row["saved_at"] . '</p>';
        echo '<button class="delete-btn">Xoá</button>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo "<p>Không có bài viết nào được lưu.</p>";
}
ob_end_flush();
?>
<style>
    .saved-posts-container {
        width: 100%;
        margin: auto;
        font-family: Arial, sans-serif;
    }
    .saved-post {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
        margin-bottom: 15px; /* Khoảng cách giữa các bài viết */
    }
    .saved-post h3 {
        margin-bottom: 5px;
    }
    .saved-post p {
        margin: 5px 0;
    }
    .delete-btn {
        background: #e4e6eb;
        color: black;
        padding: 8px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: background 0.3s;
    }
    .delete-btn:hover {
        background: #d8dadf;
    }

</style>
<script>
  function toggleReadMore(postId) {
    var content = document.getElementById(postId);
    var dots = content.querySelector('.dots');
    var readMoreBtn = content.nextElementSibling;
    
    if (content.style.webkitLineClamp) {
      content.style.display = "block";
      content.style.webkitLineClamp = "unset";
      dots.style.display = "none";
      readMoreBtn.textContent = "Thu gọn";
    } else {
      content.style.display = "-webkit-box";
      content.style.webkitLineClamp = "3";
      dots.style.display = "inline";
      readMoreBtn.textContent = "Xem thêm";
    }
  }

</script>
