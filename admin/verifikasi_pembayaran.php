<?php
include('../session.php');
include('../config.php');

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$pembayaran_id = $_GET['id'] ?? null;
$aksi = $_GET['aksi'] ?? null;

if ($pembayaran_id && $aksi == 'konfirmasi') {
    // Update status dan isi tanggal_bayar saat dikonfirmasi
    $query = "UPDATE pembayaran 
              SET status = 'dikonfirmasi', tanggal_bayar = CURDATE() 
              WHERE pembayaran_id = $pembayaran_id AND status = 'menunggu'";

    if (mysqli_query($con, $query)) {
        // echo "✅ Pembayaran berhasil dikonfirmasi dan tanggal bayar diisi otomatis.";
    } else {
        echo "❌ Gagal mengkonfirmasi pembayaran: " . mysqli_error($con);
    }

    header("Location: ./konfirmasi_pembayaran.php");
} else {
    echo "❌ ID pembayaran atau aksi tidak valid.";
}
