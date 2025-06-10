<?php
include '../session.php';
include '../config.php';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

mysqli_query($con, "DELETE FROM items WHERE item_id = $id AND user_id = $user_id");
header("Location: kelola_item.php");
exit;
?>
