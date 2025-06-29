<?php
include('../session.php');
include('../config.php');

// Ambil parameter dari URL
$booking_id = intval($_GET['id']);
$aksi = $_GET['aksi'];

// Tentukan status baru berdasarkan aksi
$status_baru = ($aksi === 'terima') ? 'disetujui' : 'ditolak';

// Update status booking
$sql = "UPDATE booking SET status = ? WHERE booking_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $status_baru, $booking_id);
$stmt->execute();

// Redirect kembali ke halaman daftar
header("Location: daftar_pemesanan.php");
exit;
