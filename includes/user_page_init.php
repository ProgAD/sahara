<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
    header('Location: login.html');
    exit;
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];
$user_phone = $_SESSION['phone'];
$role = $_SESSION['role'];
if ($role != 'user') {
    header('Location: actions/auth/logout.php');
    exit;
}

?>