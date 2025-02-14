<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require_once 'db_connection.php';
    require '../vendor/autoload.php'; 
    date_default_timezone_set('Asia/Ho_Chi_Minh');


    if(isset($_POST['sendEmailResetPw'])){
        $email = $_POST['email'];

        // Kiểm tra email có tồn tại trong CSDL không
        $query = "SELECT email FROM users WHERE email = '$email' LIMIT 1";
        $result = $conn->query($query);

        if($result -> num_rows > 0){
            // Tạo token reset
            $token = bin2hex(random_bytes(50));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token hết hạn sau 1 giờ

            // Lưu token vào bảng password_resets
            $query = "INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires')";

            if ($conn->query($query) === TRUE) {
                $host = $_SERVER['HTTP_HOST'];
                $resetLink = "http://$host/WebAppMxh/page/reset_password.php?token=$token";
    
                // Gửi email với PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Cấu hình SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // SMTP server (vd: Gmail)
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nguyenatu23@gmail.com'; // Email của bạn
                    $mail->Password = 'tkio emnp tirc hzed'; // Mật khẩu ứng dụng (app password)
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Mã hóa
                    $mail->Port = 587; // Cổng SMTP
        
                    // Người gửi
                    $mail->setFrom('reset-password@peace.com', 'Peace');
                    // Người nhận
                    $mail->addAddress($email); // Email của người nhận
        
                    // Nội dung
                    $mail->isHTML(true);
                    $mail->Subject = 'Reset your password!';
                    $mail->Body = "Nhấn vào liên kết sau để đặt lại mật khẩu của bạn: <a href=$resetLink>$resetLink</a>";
        
                    $mail->send();
                    echo 'Email đặt lại mật khẩu đã được gửi!';
                } catch (Exception $e) {
                    echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
                }
            } else {
                echo "Lỗi khi lưu token vào CSDL.";
            }
        } else {
            echo "Lỗi khi lưu token vào CSDL.";
        }
    }
?>