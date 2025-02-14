<?php
    require_once '../ajax_action.php';

    if(isset($_POST['searchUser'])){
        $userId = $_SESSION['user_id'];
        $content = $_POST['content'];

        $searchQuery = 'SELECT user_id , full_name , avatar FROM users WHERE user_id != ? AND full_name like CONCAT(?,"%") LIMIT 5;';
        $stmt = $conn->prepare($searchQuery);
        $stmt->bind_param("is", $userId,$content);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '';

        if($result->num_rows > 0){
            $output .= '<ul class="sidebar__wrapper-list">';
            while($row = $result->fetch_assoc()){
                $fullName = $row['full_name'];
                $avatar = !empty($row['avatar']) ? $row['avatar'] : '../img/default-avatar.png'; // Ảnh mặc định
                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
                $output .= "
                    <a href='$profileUrl'>
                        <li class='sidebar__wrapper-item'>
                            <img src='$avatar'>
                            <span class='title'>$fullName</span>
                        </li>
                    </a>
                ";
            }
            $output .= '<ul>';
        }else{
            $output = '<p style="text-align:center; font-size:15px;">Không tìm thấy kết quả</p>';
        }
        echo $output;
        exit();
    }