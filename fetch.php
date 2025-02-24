<?php
header('Content-Type: application/json'); // Đảm bảo trả về JSON
include 'connect.php';

$sql = "SELECT * FROM savedposts ORDER BY saved_at DESC";
$result = $conn->query($sql);

$posts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    echo json_encode($posts);
} else {
    echo json_encode([]); // Trả về mảng rỗng nếu không có dữ liệu
}

$conn->close();
?>
