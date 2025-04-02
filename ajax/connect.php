<?php
// Kết nối tới MySQL
$conn = new mysqli("localhost", "root", "", "social_media_web");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Nếu lỗi, dừng và in lỗi
}
?>