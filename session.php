<?php
// session.php (include this in all files to get current user)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ./');
    exit();
}
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
?>
