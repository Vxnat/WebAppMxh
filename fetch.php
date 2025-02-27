<?php
header("Content-Type: application/json");
include 'connect.php';

$sql = "SELECT savedposts.saved_id, savedposts.user_id, savedposts.post_id, savedposts.saved_at FROM savedposts";
$result = $conn->query($sql);

$saved_posts = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $saved_posts[] = $row;
    }
}

$conn->close();

echo json_encode($saved_posts);
?>
