<?php
    require_once '../db_connection.php';
    session_start();
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    // Sign In
    if (isset($_POST['signIn'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $query = "SELECT user_id, email, password, full_name, avatar FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['avatar'] = $row['avatar'] != '' ? $row['avatar'] : '../img/default-avatar.png';

                echo json_encode(["success" => true, "redirect" => "../page/home.php"]);
            } else {
                echo json_encode(["success" => false, "message" => "Mật khẩu không chính xác!"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Email không tồn tại!"]);
        }
        exit();
    }

    // Sign Up
    if (isset($_POST['signUp'])) {
        $email = trim($_POST['email']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Mã hóa mật khẩu
        $parts = explode("@", $email);
        $fullName = $parts[0];

        // Kiểm tra email đã tồn tại chưa
        $checkQuery = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $checkResult = $stmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Email đã tồn tại!"]);
            exit();
        }

        // Thêm tài khoản mới
        $query = "INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $email, $password, $fullName);
        $result = $stmt->execute();

        if ($result) {
            // Lấy thông tin user mới đăng ký
            $userQuery = "SELECT user_id, email, full_name, avatar FROM users WHERE email = ?";
            $stmt = $conn->prepare($userQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $userResult = $stmt->get_result();

            if ($userResult->num_rows > 0) {
                $row = $userResult->fetch_assoc();
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['avatar'] = $row['avatar'] != '' ? $row['avatar'] : '../img/default-avatar.png';

                echo json_encode(["success" => true, "redirect" => "../page/home.php"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Có lỗi xảy ra khi tạo tài khoản!"]);
        }
        exit();
    }

    // Logout
    if (isset($_POST['logout'])) {
        session_destroy(); // Xóa toàn bộ session
        echo json_encode(["success" => true, "redirect" => "../page/index.php"]);
        exit();
    }

    // Update Password
    if (isset($_POST['updatePw'])) {
        $token = $_POST['token'];
    
        // Kiểm tra token có tồn tại và còn hiệu lực
        $query = "SELECT email FROM password_resets WHERE token = '$token' AND expires_at > NOW() LIMIT 1";
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $email = $user['email'];

            $newPassword = $_POST['pw'];
            // Cập nhật mật khẩu trong bảng users
            $query = "UPDATE users SET password = '$newPassword' WHERE email = '$email'";
            if ($conn->query($query) === TRUE) {
                // Xóa token reset sau khi thành công
                $query = "DELETE FROM password_resets WHERE email = '$email'";
                $conn->query($query);

                echo "Đặt lại mật khẩu thành công!";
            } else {
                echo "Lỗi khi đặt lại mật khẩu.";
            }
        } else {
            echo "Token không hợp lệ hoặc đã hết hạn.";
        }
    }