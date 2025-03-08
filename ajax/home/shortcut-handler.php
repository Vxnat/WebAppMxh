<?php
    require_once '../ajax_action.php';
    
    if(isset($_POST['fetchShortcut'])){
        $userId = $_SESSION['user_id']; // ID người dùng hiện tại

        $shortcutQuery = "SELECT s.shortcut_id , u.user_id , u.full_name , u.avatar FROM `shortcuts` s
                JOIN users u on u.user_id = s.reference_id
                WHERE s.type = 'user' AND s.user_id = ?;";
        
        $stmt = $conn->prepare($shortcutQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '';

        if($result->num_rows > 0){
            $output .= '<div class="sidebar__wrapper-content">
            <ul class="sidebar__wrapper-list">';
            while($row = $result->fetch_assoc()){
                $shortcut_id = $row['shortcut_id'];
                $avatar = $row['avatar'] != '' ? $row['avatar'] : '../img/default-avatar.png';
                $fullName = $row['full_name'];
                $profileUrl = "profile.php?user_id=" . $row['user_id']; // đường link đến profile
                $output .= "
                    <li class='sidebar__wrapper-item' style='position:relative;' data-shortcut-id='$shortcut_id'>
                                    <a href='$profileUrl'><img src='$avatar'
                                        alt='' /></a>
                                    <a href='$profileUrl'><div  class='sidebar__wrapper-item_content'>
                                        <span class='name'>$fullName</span>
                                    </div>
                                    </a>
                                    <button type='button' class='remove-shortcut'><i style='color:red' class=\"ri-close-circle-fill\"></i></button>
                            </li>
                        ";
            }

            $output .= '</ul></div>';
        }

        echo $output;
    }

    if(isset($_POST['removeShortcut'])){
        $shortcutId = $_POST['shortcutId'];

        $removeShortcutQuery = "DELETE FROM shortcuts WHERE shortcut_id = ? LIMIT 1";
        $stmt = $conn->prepare($removeShortcutQuery);
        $stmt->bind_param("i", $shortcutId);
        $stmt->execute();

        echo true;
    }