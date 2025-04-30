<?php
require_once '../ajax_action.php';

// Lay du lieu cho phan Privacy
if (isset($_POST['getPrivacySections'])) {
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT * FROM User_Privacy_Settings WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $privacySections = array();
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'privacySections' => $row
        ]);
        exit();
    }else{
        echo json_encode([
            'success' => false,
            'error' => 'No privacy settings found.'
        ]);
        exit();
    }
}

// Cập nhật User Privacy Setting
if (isset($_POST['updatePrivacySetting'])) {
    $user_id = $_SESSION['user_id'];
    $update_fields = [];
    $params = [];
    $types = "";

    // Kiểm tra từng giá trị có tồn tại trong $_POST hay không
    if (isset($_POST['showEmail'])) {
        $update_fields[] = "show_email = ?";
        $params[] = (int) $_POST['showEmail']; // Chuyển thành int (1 hoặc 0)
        $types .= "i";
    }
    if (isset($_POST['showAvatar'])) {
        $update_fields[] = "show_avatar = ?";
        $params[] = (int) $_POST['showAvatar'];
        $types .= "i";
    }

    if (empty($update_fields)) {
        echo json_encode(['success' => false, 'error' => 'No data to update.']);
        exit();
    }

    // Cap nhat privacy
    $query = "UPDATE User_Privacy_Settings SET " . implode(", ", $update_fields) . " WHERE user_id = ?";
    $params[] = $user_id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Query preparation failed.']);
        exit();
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Privacy settings updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed.']);
    }
    exit();
}

// Change Password 
if (isset($_POST['changePassword'])) {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['oldPassword'];
    $new_password = $_POST['newPassword'];

    $query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Chuyen thanh dang ma hoa
        $hashed_password = $row['password'];

        // So sanh mat khau trong csdl
        if (password_verify($old_password, $hashed_password)) {
            // Ma hoa mat khau moi
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                // Tien hanh cap nhat mat khau moi
                $query = "UPDATE users SET password = ? WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", $hashed_new_password, $user_id);
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => 'Thay đổi Password thành công !']);
        }else{
            echo json_encode(['success' => false, 'error' => 'OldPassword không chính xác.']);
        }
    }
    exit();
}
                