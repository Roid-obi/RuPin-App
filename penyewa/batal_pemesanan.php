<?php
include('../session.php');
include('../config.php');

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "UPDATE pemesanan SET status = 'dibatalkan' WHERE booking_id = ? AND user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();

header("Location: status_pemesanan.php?msg=dibatalkan");
