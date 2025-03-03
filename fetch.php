<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
ob_start();
include 'connect.php';

$query = "SELECT savedposts.saved_id, savedposts.post_id, savedposts.saved_at, users.full_name, posts.content 
          FROM savedposts 
          JOIN posts ON savedposts.post_id = posts.post_id 
          JOIN users ON posts.user_id = users.user_id 
          ORDER BY savedposts.saved_at DESC";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='saved-item'>";
        echo "<h3>Nội dung bài viết: " . htmlspecialchars($row['content']) . "</h3>";
        echo "<p>Người đăng: " . htmlspecialchars($row['full_name']) . "</p>";
        echo "<p>Ngày lưu: " . htmlspecialchars($row['saved_at']) . "</p>";
        echo "<form method='POST' action='delete.php'>";
        echo "<input type='hidden' name='saved_id' value='" . htmlspecialchars($row['saved_id']) . "'>";
        echo "<button type='submit' name='delete'>Xoá</button>";
        echo "<button type='submit' name='delete'>Xem thêm</button>";
        echo "</form>";
        echo "</div>";
    }
} else {
    echo "<p>Không có bài viết nào đã lưu</p>";
}
ob_end_flush();
?>

</body>
</html>