<?php
include('../session.php');
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = intval($_POST['booking_id']);

    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        die("Upload gagal.");
    }

    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $newName = 'bukti_' . time() . '.' . $ext;
    $path = $uploadDir . $newName;

    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $path)) {
        $tanggal = date('Y-m-d');
        $status = 'menunggu';

        $stmt = $con->prepare("UPDATE pembayaran SET bukti = ?, tanggal_bayar = ?, status = ? WHERE booking_id = ?");
        $stmt->bind_param("sssi", $newName, $tanggal, $status, $booking_id);

        if ($stmt->execute()) {
            header("Location: konfirmasi_pembayaran.php?booking_id=" . $booking_id);
            exit;
        } else {
            echo "❌ Gagal menyimpan ke database.";
        }
    } else {
        echo "❌ Gagal menyimpan file.";
    }
}
?>
