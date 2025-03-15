<?php
ob_start();
header("Content-Type: application/json");
include 'connect.php';

$response = ["success" => false, "message" => "Lỗi không xác định"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saved_id'])) {
        $saved_id = intval($_POST['saved_id']); // Ép kiểu tránh lỗi SQL Injection
        $query = "DELETE FROM savedposts WHERE saved_id = $saved_id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $response = ["success" => true, "message" => "Xóa bài thành công"];
        } else {
            $response = ["success" => false, "message" => "Lỗi SQL: " . mysqli_error($conn)];
        }
    } else {
        $response = ["success" => false, "message" => "Thiếu tham số saved_id"];
    }
} else {
    $response = ["success" => false, "message" => "Phương thức không hợp lệ"];
}

ob_end_clean(); // Xóa tất cả đầu ra không cần thiết
echo json_encode($response);
exit;
?>
