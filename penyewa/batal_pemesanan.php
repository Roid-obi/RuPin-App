<?php
include('../session.php');
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID booking tidak ditemukan.");
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil item_id dari booking
$sql_get = "SELECT item_id FROM booking WHERE booking_id = ? AND user_id = ? AND status = 'menunggu'";
$stmt_get = $con->prepare($sql_get);
$stmt_get->bind_param("ii", $booking_id, $user_id);
$stmt_get->execute();
$result = $stmt_get->get_result();

if ($result->num_rows === 0) {
    die("Pemesanan tidak ditemukan atau tidak dapat dibatalkan.");
}

$data = $result->fetch_assoc();
$item_id = $data['item_id'];

// Update status booking menjadi dibatalkan
$sql_update = "UPDATE booking SET status = 'dibatalkan' WHERE booking_id = ? AND user_id = ?";
$stmt_update = $con->prepare($sql_update);
$stmt_update->bind_param("ii", $booking_id, $user_id);
$stmt_update->execute();

// Update status item menjadi tersedia kembali
$sql_item = "UPDATE items SET status = 'tersedia' WHERE item_id = ?";
$stmt_item = $con->prepare($sql_item);
$stmt_item->bind_param("i", $item_id);
$stmt_item->execute();

header("Location: status_pemesanan.php?msg=dibatalkan");
exit;
?>
