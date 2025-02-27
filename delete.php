<?php
header("Content-Type: application/json");
include 'connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Chắc chắn rằng ID là số nguyên
    $sql = "DELETE FROM savedposts WHERE saved_id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Xóa thành công!";
    } else {
        echo "Lỗi khi xóa: " . $conn->error;
    }
} else {
    echo "Thiếu ID để xóa.";
}

$conn->close();
?>
