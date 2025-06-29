<?php
include('../session.php');
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pembayaran_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$aksi = $_GET['aksi'] ?? null;

if ($pembayaran_id && $aksi === 'konfirmasi') {
    // Ambil status pembayaran saat ini
    $stmt = $con->prepare("SELECT status FROM pembayaran WHERE pembayaran_id = ?");
    $stmt->bind_param("i", $pembayaran_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "❌ Pembayaran tidak ditemukan.";
        exit;
    }

    $row = $result->fetch_assoc();
    if ($row['status'] !== 'menunggu') {
        echo "❌ Pembayaran ini tidak dalam status menunggu konfirmasi.";
        exit;
    }

    // Lanjut update ke status lunas dan isi tanggal_bayar jika belum terisi
    $stmt_update = $con->prepare("UPDATE pembayaran SET status = 'lunas' WHERE pembayaran_id = ?");
    $stmt_update->bind_param("i", $pembayaran_id);

    if ($stmt_update->execute()) {
        header("Location: ./konfirmasi_pembayaran.php");
        exit;
    } else {
        echo "❌ Gagal mengonfirmasi pembayaran: " . $stmt_update->error;
        exit;
    }

} else {
    echo "❌ ID pembayaran atau aksi tidak valid.";
    exit;
}
?>
