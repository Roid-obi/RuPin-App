<?php
include('../session.php');
include('../config.php');

$booking_id = $_GET['id'];
$aksi = $_GET['aksi'];

$status_baru = ($aksi == 'terima') ? 'diterima' : 'ditolak';

$sql = "UPDATE pemesanan SET status = ? WHERE booking_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $status_baru, $booking_id);
$stmt->execute();

header("Location: daftar_pemesanan.php");
