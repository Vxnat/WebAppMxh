<?php 
session_start();
$email = '';
$password = '';
if(isset($_SESSION['email']) && $_SESSION['email'] != ''){
    $email = $_SESSION['email'];
}

if(isset($_SESSION['password']) && $_SESSION['password'] != ''){
    $password = $_SESSION['password'];
}

if($email != ''){
    $avatar = $_SESSION["avatar"];
    $logined = '<img src='.$avatar.' alt="" class="header__infor-avatar">';
}else{
header('location: ../page/index.php');
}