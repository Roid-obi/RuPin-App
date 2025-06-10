<?php
include('../session.php');
include('../config.php');

$id = $_GET['id'];
$stmt = $con->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: kelola_user.php");
