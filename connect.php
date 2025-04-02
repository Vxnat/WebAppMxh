<?php
$servername = "localhost"; // Hoặc 127.0.0.1
$username = "root";        // Tài khoản mặc định là "root" trên XAMPP
$password = "";            // Mặc định trên XAMPP là rỗng
$database = "web_mysqli"; // Tên CSDL của bạn trên phpMyAdmin

// Kết nối tới MySQL
$conn = new mysqli("localhost", "root", "", "web_mysqli");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Nếu lỗi, dừng và in lỗi
}
?>