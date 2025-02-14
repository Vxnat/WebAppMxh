<?php
    require_once '../ajax_action.php';
    
    if(isset($_POST['fetchShortcut'])){
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại

        $shortcutQuery = "SELECT s.user_id , u.full_name , u.avatar FROM `shortcuts` s
                JOIN users u on u.user_id = s.user_id
                WHERE s.type = 'user' AND s.user_id = ?";
        
        $stmt = $conn->prepare($shortcutQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '';

        if($result->num_rows > 0){
            $output .= '<div class="sidebar__wrapper-content">
            <ul class="sidebar__wrapper-list">';
            while($row = $result->fetch_assoc()){
                $avatar = $row['avatar'] != '' ? $row['avatar'] : '../img/default-avatar.png';
                $fullName = $row['full_name'];
                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
                $output .= "
                    <a href='$profileUrl'>
                                    <li class='sidebar__wrapper-item'>
                                        <img src='$avatar'
                                            alt='' />
                                        <span class='title'>$fullName</span>
                                    </li>
                    </a>
                ";
            }

            $output .= '</ul></div>';
        }

        echo $output;
    }