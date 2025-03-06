<?php
ob_start();
header("Content-Type: application/json");
include 'connect.php';

if (isset($_POST['delete'])) {
    $saved_id = $_POST['saved_id'];
    $query = "DELETE FROM savedposts WHERE saved_id = $saved_id";
    mysqli_query($conn, $query);
    header("Location: index.php");
}
ob_end_flush();
?>
