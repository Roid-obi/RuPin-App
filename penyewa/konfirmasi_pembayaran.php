<?php
include('../session.php');
include('../config.php');

if (!isset($_GET['booking_id'])) {
    die("Booking ID tidak ditemukan.");
}

$booking_id = intval($_GET['booking_id']);

$sql = "SELECT p.jumlah, p.metode, i.nama AS nama_item, i.harga_sewa
        FROM pembayaran p
        JOIN pemesanan pm ON p.booking_id = pm.booking_id
        JOIN items i ON pm.item_id = i.item_id
        WHERE p.booking_id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data pembayaran tidak ditemukan.");
}

$data = $result->fetch_assoc();
$biaya_admin = $data['harga_sewa'] * 0.05;
?>

<h2>Konfirmasi Pembayaran</h2>
<p>Item: <strong><?= htmlspecialchars($data['nama_item']) ?></strong></p>
<p>Metode Pembayaran: <strong><?= htmlspecialchars($data['metode']) ?></strong></p>
<p>Harga Sewa: <strong>Rp <?= number_format($data['harga_sewa'], 0, ',', '.') ?></strong></p>
<p>Biaya Admin (5%): <strong>Rp <?= number_format($biaya_admin, 0, ',', '.') ?></strong></p>
<p><strong>Total yang Harus Dibayar: Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></strong></p>

<p>Silakan lakukan pembayaran dan tunggu konfirmasi dari admin.</p>
<a href="status_pemesanan.php">Lihat Status Pemesanan</a>
